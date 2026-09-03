<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SchoolDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SchoolDocumentTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $operator;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $opRole = Role::firstOrCreate(['name' => 'Operator']);

        $permEdit = Permission::firstOrCreate(['name' => 'edit_customers']);
        $permShow = Permission::firstOrCreate(['name' => 'show_customers']);
        $opRole->permissions()->syncWithoutDetaching([$permEdit->id, $permShow->id]);

        $this->admin = User::create([
            'username' => 'test_admin_' . uniqid(),
            'emp_code' => 'EMP-ADM-' . uniqid(),
            'email'    => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $adminRole->id,
        ]);

        $this->operator = User::create([
            'username' => 'test_op_' . uniqid(),
            'emp_code' => 'EMP-OP-' . uniqid(),
            'email'    => 'op_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $opRole->id,
        ]);

        $this->customer = Customer::create([
            'school_code'  => 'SCH-TEST-' . rand(1000, 9999),
            'name'         => 'Test School',
            'phone_number' => '9876543210',
            'state'        => 'Maharashtra',
            'city'         => 'Mumbai',
            'created_by'   => $this->operator->id,
        ]);
    }

    public function test_non_admin_can_upload_initial_document()
    {
        $this->actingAs($this->operator);

        $file = UploadedFile::fake()->create('aadhar.pdf', 500, 'application/pdf');

        $response = $this->postJson(route('school-documents.store'), [
            'customer_id' => $this->customer->id,
            'type'        => 'aadhar',
            'file'        => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('school_documents', [
            'customer_id' => $this->customer->id,
            'type'        => 'aadhar',
        ]);
    }

    public function test_non_admin_cannot_replace_existing_document()
    {
        // Initial upload
        $doc = SchoolDocument::create([
            'customer_id' => $this->customer->id,
            'type'        => 'pan',
            'filename'    => 'school-docs/' . $this->customer->school_code . '/pan.pdf',
            'uploaded_by' => $this->operator->id,
        ]);

        $this->actingAs($this->operator);

        $file = UploadedFile::fake()->create('pan_new.pdf', 500, 'application/pdf');

        $response = $this->postJson(route('school-documents.store'), [
            'customer_id' => $this->customer->id,
            'type'        => 'pan',
            'file'        => $file,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Only administrators can replace existing documents.',
        ]);
    }

    public function test_admin_can_replace_existing_document()
    {
        $filePath = 'school-docs/' . $this->customer->school_code . '/pan.pdf';
        Storage::disk('public')->put($filePath, 'old content');

        $doc = SchoolDocument::create([
            'customer_id' => $this->customer->id,
            'type'        => 'pan',
            'filename'    => $filePath,
            'uploaded_by' => $this->operator->id,
        ]);

        $this->actingAs($this->admin);

        $file = UploadedFile::fake()->create('pan_new.pdf', 500, 'application/pdf');

        $response = $this->postJson(route('school-documents.store'), [
            'customer_id' => $this->customer->id,
            'type'        => 'pan',
            'file'        => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_non_admin_cannot_delete_document()
    {
        $doc = SchoolDocument::create([
            'customer_id' => $this->customer->id,
            'type'        => 'gst_certificate',
            'filename'    => 'school-docs/' . $this->customer->school_code . '/gst_certificate.pdf',
            'uploaded_by' => $this->operator->id,
        ]);

        $this->actingAs($this->operator);

        $response = $this->deleteJson(route('school-documents.destroy', $doc));

        $response->assertStatus(403);
        $this->assertDatabaseHas('school_documents', ['id' => $doc->id]);
    }

    public function test_admin_can_delete_document()
    {
        $filePath = 'school-docs/' . $this->customer->school_code . '/gst_certificate.pdf';
        Storage::disk('public')->put($filePath, 'content');

        $doc = SchoolDocument::create([
            'customer_id' => $this->customer->id,
            'type'        => 'gst_certificate',
            'filename'    => $filePath,
            'uploaded_by' => $this->operator->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->deleteJson(route('school-documents.destroy', $doc));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('school_documents', ['id' => $doc->id]);
    }

    public function test_document_view_route()
    {
        $filePath = 'school-docs/' . $this->customer->school_code . '/pan.pdf';
        Storage::disk('public')->put($filePath, 'file sample content');

        $doc = SchoolDocument::create([
            'customer_id' => $this->customer->id,
            'type'        => 'pan',
            'filename'    => $filePath,
            'uploaded_by' => $this->operator->id,
        ]);

        $this->actingAs($this->operator);

        $response = $this->get(route('school-documents.view', $doc));

        $response->assertStatus(200);
    }
}
