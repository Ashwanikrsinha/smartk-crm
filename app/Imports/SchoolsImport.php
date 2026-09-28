<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Designation;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SchoolsImport implements ToCollection, WithHeadingRow
{
    protected int $importedCount = 0;
    protected int $skippedCount  = 0;
    protected array $errors       = [];
    protected array $importedCodes = [];

    public function collection(Collection $rows)
    {
        $authUser = auth()->user();
        $isAdmin  = $authUser && ($authUser->isAdmin() || $authUser->isBusinessManager());

        // Cache lead sources and designations for fast lookup
        $leadSources  = LeadSource::all();
        $designations = Designation::all();

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // Accounting for 1-based indexing and header row

            // Normalize row data
            $name          = trim((string) ($row['school_name'] ?? $row['name'] ?? $row['school'] ?? ''));
            $phone         = trim((string) ($row['mobile_number'] ?? $row['mobile'] ?? $row['phone_number'] ?? $row['phone'] ?? ''));
            $state         = trim((string) ($row['state'] ?? ''));
            $city          = trim((string) ($row['city'] ?? ''));
            $pinCode       = trim((string) ($row['pin_code'] ?? $row['pincode'] ?? $row['pin'] ?? ''));
            $email         = trim((string) ($row['email_id'] ?? $row['email'] ?? ''));
            $address       = trim((string) ($row['address'] ?? $row['school_address'] ?? ''));
            $gstin         = trim((string) ($row['gstin'] ?? $row['gst_number'] ?? $row['gst'] ?? ''));
            $leadSourceRaw = trim((string) ($row['lead_source'] ?? $row['lead_from'] ?? $row['lead_source_id'] ?? ''));
            $description   = trim((string) ($row['description'] ?? $row['notes'] ?? ''));
            $contactName   = trim((string) ($row['contact_person'] ?? $row['contact_person_name'] ?? $row['person_name'] ?? $row['contact_name'] ?? ''));
            $contactNumber = trim((string) ($row['contact_number'] ?? $row['contact_no'] ?? $row['person_contact'] ?? ''));
            $designationRaw= trim((string) ($row['designation'] ?? $row['designation_id'] ?? ''));
            $empCodeRaw    = trim((string) ($row['employee_code'] ?? $row['employee_id'] ?? $row['emp_code'] ?? $row['user_id'] ?? ''));

            // Check if entire row is empty
            if (empty($name) && empty($phone) && empty($state) && empty($city) && empty($pinCode)) {
                continue;
            }

            // Validation
            $rowErrors = [];
            if (empty($name)) {
                $rowErrors[] = 'School Name is required.';
            }
            if (empty($phone)) {
                $rowErrors[] = 'Mobile Number is required.';
            }
            if (empty($state)) {
                $rowErrors[] = 'State is required.';
            }
            if (empty($city)) {
                $rowErrors[] = 'City is required.';
            }
            if (empty($pinCode)) {
                $rowErrors[] = 'Pin Code is required.';
            }

            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $rowErrors[] = "Invalid Email ID format: '{$email}'.";
            }

            if (!empty($rowErrors)) {
                $this->skippedCount++;
                $this->errors[] = "Row {$rowNum} (" . ($name ?: 'Unnamed') . '): ' . implode(' ', $rowErrors);
                continue;
            }

            // Lead Source Resolution (by ID or Name)
            $leadSourceId = null;
            if (!empty($leadSourceRaw)) {
                if (is_numeric($leadSourceRaw)) {
                    $ls = $leadSources->firstWhere('id', (int) $leadSourceRaw);
                    $leadSourceId = $ls ? $ls->id : null;
                } else {
                    $ls = $leadSources->first(fn($item) => strcasecmp($item->name, $leadSourceRaw) === 0);
                    if ($ls) {
                        $leadSourceId = $ls->id;
                    } else {
                        // Create new lead source if not existing
                        $newLs = LeadSource::create(['name' => $leadSourceRaw]);
                        $leadSources->push($newLs);
                        $leadSourceId = $newLs->id;
                    }
                }
            }

            // Designation Resolution (by ID or Name)
            $designationId = null;
            if (!empty($designationRaw)) {
                if (is_numeric($designationRaw)) {
                    $des = $designations->firstWhere('id', (int) $designationRaw);
                    $designationId = $des ? $des->id : null;
                } else {
                    $des = $designations->first(fn($item) => strcasecmp($item->name, $designationRaw) === 0);
                    if ($des) {
                        $designationId = $des->id;
                    } else {
                        $newDes = Designation::create(['name' => $designationRaw]);
                        $designations->push($newDes);
                        $designationId = $newDes->id;
                    }
                }
            }

            // User / Employee Assignment Logic
            $createdBy = $authUser ? $authUser->id : 1;
            if ($isAdmin && !empty($empCodeRaw)) {
                $targetUserQuery = User::where('emp_code', $empCodeRaw)
                    ->orWhere('username', $empCodeRaw);

                if (is_numeric($empCodeRaw)) {
                    $targetUserQuery->orWhere('id', (int) $empCodeRaw);
                }

                $targetUser = $targetUserQuery->first();

                if ($targetUser) {
                    $createdBy = $targetUser->id;
                }
            }

            try {
                DB::transaction(function () use (
                    $name,
                    $phone,
                    $state,
                    $city,
                    $pinCode,
                    $email,
                    $address,
                    $gstin,
                    $leadSourceId,
                    $description,
                    $createdBy,
                    $contactName,
                    $contactNumber,
                    $designationId
                ) {
                    $schoolCode = Customer::generateSchoolCode();

                    $customer = Customer::create([
                        'school_code'    => $schoolCode,
                        'name'           => $name,
                        'phone_number'   => $phone,
                        'email'          => !empty($email) ? $email : null,
                        'address'        => !empty($address) ? $address : null,
                        'state'          => $state,
                        'city'           => $city,
                        'pin_code'       => $pinCode,
                        'gstin'          => !empty($gstin) ? strtoupper($gstin) : null,
                        'lead_source_id' => $leadSourceId,
                        'description'    => !empty($description) ? $description : null,
                        'created_by'     => $createdBy,
                    ]);

                    // Add contact person if any details provided
                    if (!empty($contactName) || !empty($contactNumber) || !empty($designationId)) {
                        CustomerContact::create([
                            'customer_id'    => $customer->id,
                            'name'           => !empty($contactName) ? $contactName : 'Contact Person',
                            'contact_number' => !empty($contactNumber) ? $contactNumber : $phone,
                            'designation_id' => $designationId,
                        ]);
                    }

                    $this->importedCount++;
                    $this->importedCodes[] = $schoolCode;
                });
            } catch (\Throwable $e) {
                Log::error("School bulk upload failed for row {$rowNum}: " . $e->getMessage());
                $this->skippedCount++;
                $this->errors[] = "Row {$rowNum} ({$name}): Error saving record - " . $e->getMessage();
            }
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getImportedCodes(): array
    {
        return $this->importedCodes;
    }
}
