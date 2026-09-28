<?php

namespace Tests\Feature;

use App\Exports\SchoolSampleExport;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Designation;
use App\Models\LeadSource;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class SchoolBulkUploadTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $salesPerson;
    protected $salesManager;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $opRole    = Role::firstOrCreate(['name' => 'Operator']);
        $smRole    = Role::firstOrCreate(['name' => 'Manager']);

        $permBrowse = Permission::firstOrCreate(['name' => 'browse_customers']);
        $permCreate = Permission::firstOrCreate(['name' => 'create_customers']);
        $permShow   = Permission::firstOrCreate(['name' => 'show_customers']);

        $adminRole->permissions()->syncWithoutDetaching([$permBrowse->id, $permCreate->id, $permShow->id]);
        $opRole->permissions()->syncWithoutDetaching([$permBrowse->id, $permCreate->id, $permShow->id]);
        $smRole->permissions()->syncWithoutDetaching([$permBrowse->id, $permCreate->id, $permShow->id]);

        $this->admin = User::create([
            'username' => 'test_admin_' . uniqid(),
            'emp_code' => 'EMP-ADM-' . uniqid(),
            'email'    => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $adminRole->id,
        ]);

        $this->salesPerson = User::create([
            'username' => 'test_sp_' . uniqid(),
            'emp_code' => 'EMP-SP-' . uniqid(),
            'email'    => 'sp_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $opRole->id,
        ]);

        $this->salesManager = User::create([
            'username' => 'test_sm_' . uniqid(),
            'emp_code' => 'EMP-SM-' . uniqid(),
            'email'    => 'sm_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $smRole->id,
        ]);
    }

    public function test_sample_export_download_route()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('customers.sample-export'));
        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition') ?? '', 'school_bulk_upload_sample.xlsx')
        );
    }

    public function test_admin_bulk_upload_with_employee_code_assignment()
    {
        $this->actingAs($this->admin);

        $schoolName = 'Green Valley School ' . uniqid();
        $leadSourceName = 'Portal_' . uniqid();
        $designationName = 'Vice Principal ' . uniqid();

        $leadSource = LeadSource::create(['name' => $leadSourceName]);
        $designation = Designation::create(['name' => $designationName]);

        // CSV content with headers matching template
        $csvContent = implode("\n", [
            'School Name,Mobile Number,State,City,Pin Code,Email ID,Address,GSTIN,Lead Source,Description,Contact Person,Contact Number,Designation,Employee Code',
            '"' . $schoolName . '","9876500001","Punjab","Ludhiana","141001","gv@example.com","Civil Lines","03AAAAA0000A1Z5","' . $leadSource->name . '","Top CBSE school","Mr. Verma","9876500002","' . $designation->name . '","' . $this->salesPerson->emp_code . '"',
        ]);

        $file = UploadedFile::fake()->createWithContent('schools_upload.csv', $csvContent);

        $response = $this->post(route('customers.bulk-upload'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success');

        // Verify school was created and assigned to the employee in the employee code column
        $school = Customer::where('name', $schoolName)->first();
        $this->assertNotNull($school);
        $this->assertEquals('Punjab', $school->state);
        $this->assertEquals('Ludhiana', $school->city);
        $this->assertEquals('141001', $school->pin_code);
        $this->assertEquals($leadSource->id, $school->lead_source_id);
        $this->assertEquals($this->salesPerson->id, $school->created_by, 'School should be assigned to the user matching Employee Code');
        $this->assertStringStartsWith('SCH-' . date('Y') . '-', $school->school_code);

        // Verify contact person was created
        $contact = CustomerContact::where('customer_id', $school->id)->latest('id')->first();
        $this->assertNotNull($contact);
        $this->assertEquals('Mr. Verma', $contact->name);
        $this->assertEquals('9876500002', $contact->contact_number);
        $this->assertEquals($designation->id, $contact->designation_id);
    }

    public function test_sales_person_bulk_upload_assigns_to_self()
    {
        $this->actingAs($this->salesPerson);

        $schoolName = 'Sunrise Academy ' . uniqid();

        // SP uploads a CSV with an Admin's emp_code in the column
        $csvContent = implode("\n", [
            'School Name,Mobile Number,State,City,Pin Code,Email ID,Address,GSTIN,Lead Source,Description,Contact Person,Contact Number,Designation,Employee Code',
            '"' . $schoolName . '","9811122233","Rajasthan","Jaipur","302001","sunrise@example.com","Tonk Road","","","","Mrs. Gupta","9811122234","","' . $this->admin->emp_code . '"',
        ]);

        $file = UploadedFile::fake()->createWithContent('schools_sp_upload.csv', $csvContent);

        $response = $this->post(route('customers.bulk-upload'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success');

        // Verify school was created and assigned to the Sales Person (self), NOT the admin
        $school = Customer::where('name', $schoolName)->first();
        $this->assertNotNull($school);
        $this->assertEquals($this->salesPerson->id, $school->created_by, 'Sales Person upload must assign created_by to self');
    }

    public function test_bulk_upload_skips_invalid_rows_and_reports_errors()
    {
        $this->actingAs($this->admin);

        $invalidName = 'Invalid School ' . uniqid();
        $validName = 'Valid Public School ' . uniqid();

        // Row 1: Missing phone number
        // Row 2: Valid school
        $csvContent = implode("\n", [
            'School Name,Mobile Number,State,City,Pin Code,Email ID,Address,GSTIN,Lead Source,Description,Contact Person,Contact Number,Designation,Employee Code',
            '"' . $invalidName . '","","Delhi","New Delhi","110001","","","","","","","","",""',
            '"' . $validName . '","9999988888","Delhi","New Delhi","110002","","","","","","","","",""',
        ]);

        $file = UploadedFile::fake()->createWithContent('partial_schools.csv', $csvContent);

        $response = $this->post(route('customers.bulk-upload'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('import_errors');

        $this->assertNull(Customer::where('name', $invalidName)->first());
        $this->assertNotNull(Customer::where('name', $validName)->first());
    }

    public function test_customers_index_page_contains_sample_download_and_bulk_upload_buttons()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('customers.index'));
        $response->assertStatus(200);
        $response->assertSee(route('customers.sample-export'));
        $response->assertSee('Download Sample');
        $response->assertSee('Bulk Upload');
        $response->assertSee('bulkUploadModal');
    }
}
