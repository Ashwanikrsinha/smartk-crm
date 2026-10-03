<?php

namespace Tests\Feature;

use App\Exports\OpeningBalanceSampleExport;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\OpeningBalanceCollection;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SchoolOpeningBalance;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class OpeningBalanceTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $accounts;
    protected $salesPerson;
    protected $school;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole    = Role::firstOrCreate(['name' => 'Administrator']);
        $accountsRole = Role::firstOrCreate(['name' => 'Accounts']);
        $spRole       = Role::firstOrCreate(['name' => 'Operator']);

        $permBrowse = Permission::firstOrCreate(['name' => 'browse_customers']);
        $permShow   = Permission::firstOrCreate(['name' => 'show_customers']);
        $adminRole->permissions()->syncWithoutDetaching([$permBrowse->id, $permShow->id]);
        $accountsRole->permissions()->syncWithoutDetaching([$permBrowse->id, $permShow->id]);
        $spRole->permissions()->syncWithoutDetaching([$permBrowse->id, $permShow->id]);

        $this->admin = User::create([
            'username' => 'test_admin_' . uniqid(),
            'emp_code' => 'EMP-ADM-' . uniqid(),
            'email'    => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $adminRole->id,
        ]);

        $this->accounts = User::create([
            'username' => 'test_acc_' . uniqid(),
            'emp_code' => 'EMP-ACC-' . uniqid(),
            'email'    => 'acc_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $accountsRole->id,
        ]);

        $this->salesPerson = User::create([
            'username' => 'test_sp_' . uniqid(),
            'emp_code' => 'EMP-SP-' . uniqid(),
            'email'    => 'sp_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $spRole->id,
        ]);

        $this->school = Customer::create([
            'name'         => 'Test Public School ' . uniqid(),
            'school_code'  => Customer::generateSchoolCode(),
            'phone_number' => '9876543210',
            'state'        => 'Delhi',
            'city'         => 'New Delhi',
            'pin_code'     => '110001',
            'created_by'   => $this->admin->id,
        ]);
    }

    /** Test 1: Access control — Admin & Accounts allowed, SP forbidden */
    public function test_opening_balances_access_control(): void
    {
        $resAdmin = $this->actingAs($this->admin)->get(route('opening-balances.index'));
        $resAdmin->assertStatus(200);

        $resAcc = $this->actingAs($this->accounts)->get(route('opening-balances.index'));
        $resAcc->assertStatus(200);

        $resSp = $this->actingAs($this->salesPerson)->get(route('opening-balances.index'));
        $resSp->assertStatus(403);
    }

    /** Test 2: Create opening balance manually */
    public function test_create_opening_balance_manually(): void
    {
        $data = [
            'customer_id'       => $this->school->id,
            'session_year'      => 2025,
            'user_id'           => $this->salesPerson->id,
            'opening_amount'    => 500000.00,
            'opening_collected' => 100000.00,
            'notes'             => 'Historical carry-forward',
        ];

        $res = $this->actingAs($this->admin)->post(route('opening-balances.store'), $data);
        $res->assertRedirect(route('opening-balances.index'));

        $this->assertDatabaseHas('school_opening_balances', [
            'customer_id'         => $this->school->id,
            'session_year'        => 2025,
            'user_id'             => $this->salesPerson->id,
            'opening_amount'      => 500000.00,
            'opening_collected'   => 100000.00,
            'opening_outstanding' => 400000.00,
            'source'              => 'manual',
        ]);
    }

    /** Test 3: Prevent duplicate opening balance for same school and session */
    public function test_prevent_duplicate_opening_balance(): void
    {
        SchoolOpeningBalance::create([
            'customer_id'         => $this->school->id,
            'session_year'        => 2025,
            'opening_amount'      => 100000,
            'opening_collected'   => 0,
            'opening_outstanding' => 100000,
            'source'              => 'manual',
            'entered_by'          => $this->admin->id,
        ]);

        $data = [
            'customer_id'    => $this->school->id,
            'session_year'   => 2025,
            'opening_amount' => 200000,
        ];

        $res = $this->actingAs($this->admin)->post(route('opening-balances.store'), $data);
        $res->assertSessionHasErrors('customer_id');
    }

    /** Test 4: Record payment against opening balance & auto-recalculate */
    public function test_record_payment_against_opening_balance(): void
    {
        $ob = SchoolOpeningBalance::create([
            'customer_id'         => $this->school->id,
            'session_year'        => 2025,
            'opening_amount'      => 300000,
            'opening_collected'   => 0,
            'opening_outstanding' => 300000,
            'source'              => 'manual',
            'entered_by'          => $this->admin->id,
        ]);

        $payData = [
            'collected_amount' => 100000,
            'payment_mode'     => 'neft',
            'reference_number' => 'UTR12345678',
            'remarks'          => 'First installment',
            'collected_at'     => '2026-08-15',
        ];

        $res = $this->actingAs($this->accounts)->post(route('opening-balances.collect', $ob), $payData);
        $res->assertSessionHas('success');

        $ob->refresh();
        $this->assertEquals(100000, (float) $ob->opening_collected);
        $this->assertEquals(200000, (float) $ob->opening_outstanding);

        $this->assertDatabaseHas('opening_balance_collections', [
            'opening_balance_id' => $ob->id,
            'collected_amount'   => 100000,
            'payment_mode'       => 'neft',
            'reference_number'   => 'UTR12345678',
            'collected_by'       => $this->accounts->id,
        ]);
    }

    /** Test 5: Delete collection recalculates parent */
    public function test_delete_collection_recalculates_parent(): void
    {
        $ob = SchoolOpeningBalance::create([
            'customer_id'         => $this->school->id,
            'session_year'        => 2025,
            'opening_amount'      => 300000,
            'opening_collected'   => 0,
            'opening_outstanding' => 300000,
            'source'              => 'manual',
            'entered_by'          => $this->admin->id,
        ]);

        $col = OpeningBalanceCollection::create([
            'opening_balance_id' => $ob->id,
            'collected_amount'   => 50000,
            'payment_mode'       => 'cash',
            'collected_by'       => $this->admin->id,
            'collected_at'       => now(),
        ]);

        $ob->refresh();
        $this->assertEquals(50000, (float) $ob->opening_collected);
        $this->assertEquals(250000, (float) $ob->opening_outstanding);

        $res = $this->actingAs($this->admin)->delete(route('opening-balance-collections.destroy', $col));
        $res->assertSessionHas('success');

        $ob->refresh();
        $this->assertEquals(0, (float) $ob->opening_collected);
        $this->assertEquals(300000, (float) $ob->opening_outstanding);
    }

    /** Test 6: Sample Excel template download */
    public function test_download_sample_excel(): void
    {
        $res = $this->actingAs($this->admin)->get(route('opening-balances.sample'));
        $res->assertStatus(200);
        $this->assertTrue(
            str_contains($res->headers->get('content-disposition') ?? '', 'SmartK_Opening_Balance_Template.xlsx')
        );
    }

    /** Test 7: Bulk upload Excel import */
    public function test_bulk_upload_opening_balances(): void
    {
        $csvContent = implode("\n", [
            'School Code,School Name,SP Employee Code,Opening Amount,Already Collected,Notes',
            "{$this->school->school_code},\"{$this->school->name}\",{$this->salesPerson->emp_code},450000,50000,Bulk uploaded balance",
        ]);

        $file = UploadedFile::fake()->createWithContent('test_ob.csv', $csvContent);

        $res = $this->actingAs($this->admin)->post(route('opening-balances.bulk-upload'), [
            'session_year' => 2025,
            'file'         => $file,
        ]);

        $res->assertRedirect();
        $res->assertSessionHas('success');

        $this->assertDatabaseHas('school_opening_balances', [
            'customer_id'         => $this->school->id,
            'session_year'        => 2025,
            'user_id'             => $this->salesPerson->id,
            'opening_amount'      => 450000.00,
            'opening_collected'   => 50000.00,
            'opening_outstanding' => 400000.00,
            'source'              => 'manual',
        ]);
    }

    /** Test 8: Auto carry-forward Artisan command */
    public function test_auto_carry_forward_artisan_command(): void
    {
        // Create an approved PO in session 2024-25 (closing session: 2024-07-01 to 2025-06-30)
        $invoice = Invoice::create([
            'invoice_number'     => Invoice::invoiceNumber(),
            'po_number'          => Invoice::generatePoNumber(),
            'invoice_date'       => '2025-03-15',
            'delivery_due_date'  => '2025-04-15',
            'customer_id'        => $this->school->id,
            'user_id'            => $this->salesPerson->id,
            'amount'             => 800000,
            'billing_amount'     => 600000,
            'collected_amount'   => 500000,
            'outstanding_amount' => 100000,
            'pending_po_amount'  => 200000,
            'status'             => Invoice::STATUS_APPROVED,
        ]);

        // Run carry-forward command for closing year 2024 (creating 2025-26 opening balance)
        $exitCode = \Illuminate\Support\Facades\Artisan::call('crm:carry-forward-balances', ['--closing-year' => 2024]);
        $this->assertEquals(0, $exitCode);

        // Expected outstanding = 800,000 - 500,000 = 300,000
        $this->assertDatabaseHas('school_opening_balances', [
            'customer_id'         => $this->school->id,
            'session_year'        => 2025,
            'user_id'             => $this->salesPerson->id,
            'opening_amount'      => 300000.00,
            'opening_billed'      => 600000.00,
            'opening_collected'   => 0.00,
            'opening_outstanding' => 300000.00,
            'source'              => 'auto',
        ]);
    }

    /** Test 9: School profile displays Opening Balance card & includes it in financials */
    public function test_school_profile_shows_opening_balance(): void
    {
        SchoolOpeningBalance::create([
            'customer_id'         => $this->school->id,
            'session_year'        => 2025,
            'opening_amount'      => 250000,
            'opening_collected'   => 50000,
            'opening_outstanding' => 200000,
            'source'              => 'manual',
            'entered_by'          => $this->admin->id,
        ]);

        $res = $this->actingAs($this->admin)->get(route('customers.show', $this->school));
        $res->assertStatus(200);
        $res->assertSee('Opening Balance (Carry-Forward)');
        $res->assertSee('250,000');
        $res->assertSee('200,000');
    }

    /** Test 10: Reports page includes Opening Balance rows */
    public function test_reports_page_includes_opening_balance(): void
    {
        SchoolOpeningBalance::create([
            'customer_id'         => $this->school->id,
            'session_year'        => 2025,
            'user_id'             => $this->salesPerson->id,
            'opening_amount'      => 200000,
            'opening_billed'      => 200000,
            'opening_collected'   => 50000,
            'opening_outstanding' => 150000,
            'source'              => 'manual',
            'entered_by'          => $this->admin->id,
        ]);

        $res = $this->actingAs($this->admin)->get(route('reports.index', ['session_year' => 2025]));
        $res->assertStatus(200);
        $res->assertSee('Opening Balance');
        $res->assertSee($this->school->name);
    }

    /** Test 11: PO Excel export downloads without error when Opening Balance rows exist */
    public function test_reports_export_includes_opening_balance(): void
    {
        SchoolOpeningBalance::create([
            'customer_id'         => $this->school->id,
            'session_year'        => 2025,
            'user_id'             => $this->salesPerson->id,
            'opening_amount'      => 350000,
            'opening_billed'      => 350000,
            'opening_collected'   => 50000,
            'opening_outstanding' => 300000,
            'source'              => 'manual',
            'entered_by'          => $this->admin->id,
        ]);

        $permExport = Permission::firstOrCreate(['name' => 'export_reports']);
        $this->admin->role->permissions()->syncWithoutDetaching([$permExport->id]);

        $res = $this->actingAs($this->admin)->get(route('reports.export', ['session_year' => 2025]));
        $res->assertStatus(200);
        $this->assertTrue(
            str_contains($res->headers->get('content-disposition') ?? '', 'SmartK_Report_')
        );
    }

    /** Test 12: Sales Person dashboard shows Opening Outstanding card and opening balance rows */
    public function test_sales_person_dashboard_reflects_opening_balances(): void
    {
        $sessionYear = ((int) date('n') >= 7) ? (int) date('Y') : (int) date('Y') - 1;
        $sessionStr  = $sessionYear . '-' . ($sessionYear + 1);

        SchoolOpeningBalance::create([
            'customer_id'         => $this->school->id,
            'session_year'        => $sessionYear,
            'user_id'             => $this->salesPerson->id,
            'opening_amount'      => 400000,
            'opening_billed'      => 400000,
            'opening_collected'   => 100000,
            'opening_outstanding' => 300000,
            'source'              => 'manual',
            'entered_by'          => $this->admin->id,
        ]);

        $res = $this->actingAs($this->salesPerson)->get(route('dashboard', ['session' => $sessionStr]));
        $res->assertStatus(200);
        $res->assertSee('Opening Outstanding');
        $res->assertSee('300,000');
        $res->assertSee('Carry Forward');
        $res->assertSee($this->school->name);
    }

    /** Test 13: Sales Manager & Admin dashboards reflect team opening balances */
    public function test_manager_and_admin_dashboards_reflect_opening_balances(): void
    {
        $sessionYear = ((int) date('n') >= 7) ? (int) date('Y') : (int) date('Y') - 1;
        $sessionStr  = $sessionYear . '-' . ($sessionYear + 1);

        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $manager = User::create([
            'username' => 'test_sm_' . uniqid(),
            'emp_code' => 'EMP-SM-' . uniqid(),
            'email'    => 'sm_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $managerRole->id,
        ]);
        $this->salesPerson->update(['reportive_id' => $manager->id]);

        SchoolOpeningBalance::create([
            'customer_id'         => $this->school->id,
            'session_year'        => $sessionYear,
            'user_id'             => $this->salesPerson->id,
            'opening_amount'      => 600000,
            'opening_billed'      => 600000,
            'opening_collected'   => 200000,
            'opening_outstanding' => 400000,
            'source'              => 'manual',
            'entered_by'          => $this->admin->id,
        ]);

        // Manager Dashboard
        $resSm = $this->actingAs($manager)->get(route('dashboard', ['session' => $sessionStr]));
        $resSm->assertStatus(200);
        $resSm->assertSee('Opening Outstanding');
        $resSm->assertSee('400,000');
        $resSm->assertSee($this->school->name);

        // Admin Dashboard
        $resAdmin = $this->actingAs($this->admin)->get(route('dashboard', ['session' => $sessionStr]));
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('Opening Outstanding');
        $resAdmin->assertSee('Opening Balances');
    }
}
