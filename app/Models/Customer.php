<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;


    public function segment()
    {
        return $this->belongsTo(Segment::class, 'segment_id')->withDefault(['name' => '']);
    }

    public function contacts()
    {
        return $this->hasMany(CustomerContact::class, 'customer_id');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'customer_id')->orderBy('visit_date', 'DESC');
    }


    public function types()
    {
        $ids = explode(',', $this->customer_types);
        return CustomerType::find($ids) ?? null;
    }


    public function createContacts($request)
    {

        foreach ($request->person_name as $i => $name) {
            $this->contacts()->create([
                'name' => $name,
                'birth_date' => $request->birth_date[$i],
                'marriage_date' => $request->marriage_date[$i],
                'contact_number' => $request->contact_number[$i],
                'designation_id' => $request->designation_id[$i],
            ]);
        }
    }
    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // public function contacts()
    // {
    //     return $this->hasMany(Contact::class, 'customer_id');
    // }

    // public function visits()
    // {
    //     return $this->hasMany(Visit::class, 'customer_id');
    // }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    public function documents()
    {
        return $this->hasMany(SchoolDocument::class, 'customer_id');
    }

    // -------------------------------------------------------
    // SCHOOL CODE GENERATOR
    // SCH-2025-0001 format, unique, auto on creation
    // -------------------------------------------------------

    /**
     * Generate next school code.
     * Call this in CustomerController::store() before creating.
     */
    public static function generateSchoolCode(): string
    {
        $year  = date('Y');
        $last  = self::whereYear('created_at', $year)->max('id') ?? 0;
        $seq   = str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        return "SCH-{$year}-{$seq}";
    }

    // -------------------------------------------------------
    // FINANCIAL AGGREGATES (for SP/SM dashboard school rows)
    // -------------------------------------------------------

    /**
     * A — Total PO Amount across all POs for this school
     */
    public function totalPoAmount(): float
    {
        return (float) $this->invoices()->sum('amount');
    }

    /**
     * B — Total Billing Amount across all POs
     */
    public function totalBillingAmount(): float
    {
        return (float) $this->invoices()->sum('billing_amount');
    }

    /**
     * C — Total Pending PO Amount (A - B)
     */
    public function totalPendingPoAmount(): float
    {
        return $this->totalPoAmount() - $this->totalBillingAmount();
    }

    /**
     * D — Total Collected across all POs
     */
    public function totalCollected(): float
    {
        return (float) $this->invoices()->sum('collected_amount');
    }

    /**
     * E — Total Outstanding (B - D)
     */
    public function totalOutstanding(): float
    {
        return $this->totalBillingAmount() - $this->totalCollected();
    }
}
