<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\OpeningBalanceCollection;
use App\Models\Role;
use App\Models\SchoolOpeningBalance;
use App\Models\User;
use App\Services\PoDocumentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PoExportHierarchyAndOpeningBalanceTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $manager;
    protected User $sp;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $spRole = Role::firstOrCreate(['name' => 'Operator']);

        $this->admin = User::create([
            'username' => 'test_adm_' . uniqid(),
            'emp_code' => 'ADM_' . uniqid(),
            'email'    => 'adm_' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
            'role_id'  => $adminRole->id,
        ]);

        $this->manager = User::create([
            'username' => 'test_mgr_' . uniqid(),
            'emp_code' => 'MGR_' . uniqid(),
            'email'    => 'mgr_' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
            'role_id'  => $managerRole->id,
        ]);

        $this->sp = User::create([
            'username'     => 'test_sp_' . uniqid(),
            'emp_code'     => 'SP_' . uniqid(),
            'email'        => 'sp_' . uniqid() . '@example.com',
            'password'     => bcrypt('secret'),
            'role_id'      => $spRole->id,
            'reportive_id' => $this->manager->id,
        ]);

        $this->customer = Customer::create([
            'name'        => 'Hierarchy School ' . uniqid(),
            'school_code' => 'SCH-HIER-' . uniqid(),
            'address'     => '456 MG Road',
            'city'        => 'Delhi',
            'state'       => 'Delhi',
            'pin_code'    => '110001',
        ]);
    }

    /**
     * 1. PO Docx data structure has po_date as invoice_date and contains order_type.
     */
    public function test_po_document_build_data_includes_order_type_and_po_date()
    {
        $invoice = Invoice::create([
            'po_number'    => 'PO-TEST-' . rand(1000, 9999),
            'invoice_number' => rand(100000, 999999),
            'invoice_date' => '2026-08-15',
            'delivery_due_date' => '2026-09-01',
            'customer_id'  => $this->customer->id,
            'user_id'      => $this->sp->id,
            'co_branding'  => true,
            'order_type'   => Invoice::ORDER_TYPE_REORDER,
            'status'       => Invoice::STATUS_APPROVED,
            'amount'       => 50000.00,
            'pending_po_amount' => 50000.00,
            'approved_at'  => now(),
        ]);

        $service = app(PoDocumentService::class);
        $method = new \ReflectionMethod(PoDocumentService::class, 'buildData');
        $method->setAccessible(true);
        $data = $method->invoke($service, $invoice);

        $this->assertEquals('15 August, 2026', $data['po_date']);
        $this->assertEquals(Invoice::ORDER_TYPE_REORDER, $data['order_type']);
        $this->assertTrue($data['co_branding']);
    }

    /**
     * 2. PO Approval email highlights PO Date, Order Type, Co-Branding.
     */
    public function test_po_approval_email_renders_order_type_and_po_date()
    {
        $invoice = Invoice::create([
            'po_number'    => 'PO-MAIL-' . rand(1000, 9999),
            'invoice_number' => rand(100000, 999999),
            'invoice_date' => '2026-09-10',
            'customer_id'  => $this->customer->id,
            'user_id'      => $this->sp->id,
            'co_branding'  => false,
            'order_type'   => Invoice::ORDER_TYPE_RENEWAL,
            'status'       => Invoice::STATUS_APPROVED,
            'amount'       => 75000.00,
            'pending_po_amount' => 75000.00,
        ]);

        $rendered = view('emails.po-approved', ['invoice' => $invoice])->render();

        $this->assertStringContainsString('PO Date:</strong> 10 Sep, 2026', $rendered);
        $this->assertStringContainsString('Order Type:</strong> Renewal', $rendered);
        $this->assertStringContainsString('Co-Branding:</strong> No', $rendered);
    }

    /**
     * 3. Filtering by Sales Manager returns subordinate SP data in PO Report and Manager Dashboard.
     */
    public function test_manager_filter_returns_subordinate_data_in_report_and_dashboard()
    {
        // Invoice created by SP who reports to Manager
        $invoice = Invoice::create([
            'po_number'    => 'PO-MGR-' . rand(1000, 9999),
            'invoice_number' => rand(100000, 999999),
            'invoice_date' => '2026-08-20',
            'customer_id'  => $this->customer->id,
            'user_id'      => $this->sp->id,
            'status'       => Invoice::STATUS_APPROVED,
            'amount'       => 80000.00,
            'collected_amount' => 30000.00,
            'outstanding_amount' => 50000.00,
            'pending_po_amount' => 80000.00,
        ]);

        // Opening balance assigned to SP
        $ob = SchoolOpeningBalance::create([
            'customer_id'         => $this->customer->id,
            'session_year'        => 2026,
            'user_id'             => $this->sp->id,
            'opening_amount'      => 40000.00,
            'opening_billed'      => 40000.00,
            'opening_collected'   => 10000.00,
            'opening_outstanding' => 30000.00,
            'source'              => 'manual',
            'status'              => 'carry_forward',
            'entered_by'          => $this->admin->id,
        ]);

        // Test User::subordinateAndSelfIds()
        $subordinateIds = $this->manager->subordinateAndSelfIds();
        $this->assertContains($this->manager->id, $subordinateIds);
        $this->assertContains($this->sp->id, $subordinateIds);

        // Test PO Report filtered by Manager (sp_id = manager->id)
        $this->actingAs($this->admin);
        $response = $this->get(route('reports.index', ['sp_id' => $this->manager->id, 'session' => '2026-2027']));
        $response->assertStatus(200);
        $response->assertSee($invoice->po_number);
        $response->assertSee($this->customer->name);

        // Test Manager Dashboard filtered by Manager's own ID
        $this->actingAs($this->manager);
        $dashResponse = $this->get(route('dashboard', ['sp_id' => $this->manager->id, 'session' => '2026-2027']));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee($invoice->po_number);
    }

    /**
     * 4. Settled opening balance from past session collected during current session remains visible.
     */
    public function test_settled_opening_balance_collected_in_current_session_remains_visible()
    {
        $pastSessionYear = 2025;
        $currentSessionYear = 2026;

        $ob = SchoolOpeningBalance::create([
            'customer_id'         => $this->customer->id,
            'session_year'        => $pastSessionYear,
            'user_id'             => $this->sp->id,
            'opening_amount'      => 100000.00,
            'opening_billed'      => 100000.00,
            'opening_collected'   => 0.00,
            'opening_outstanding' => 100000.00,
            'source'              => 'manual',
            'status'              => 'carry_forward',
            'entered_by'          => $this->admin->id,
        ]);

        // Record collection in current session (e.g. October 2026) that fully settles it
        OpeningBalanceCollection::create([
            'opening_balance_id' => $ob->id,
            'collected_amount'   => 100000.00,
            'billed_amount'      => 0.00,
            'gst_amount'         => 0.00,
            'payment_mode'       => 'neft',
            'reference_number'   => 'SETTLE-2026-FULL',
            'collected_by'       => $this->admin->id,
            'collected_at'       => '2026-10-10',
        ]);

        $ob->refresh();
        $this->assertEquals(100000.00, (float) $ob->opening_collected);
        $this->assertEquals(0.00, (float) $ob->opening_outstanding);

        // Verify scopeForSession(2026) includes this record even though outstanding is 0
        $session2026Ids = SchoolOpeningBalance::forSession($currentSessionYear)->pluck('id')->toArray();
        $this->assertContains($ob->id, $session2026Ids);

        // Verify it appears in PO report and collection totals
        $this->actingAs($this->admin);
        $response = $this->get(route('reports.index', ['session' => '2026-2027']));
        $response->assertStatus(200);
        $response->assertSee('₹100,000.00');

        // Verify Manager Dashboard includes the opening collection
        $this->actingAs($this->manager);
        $dashResponse = $this->get(route('dashboard', ['session' => '2026-2027']));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('₹100,000');
    }

    /**
     * 5. PO report supports pagination and per_page selection.
     */
    public function test_po_report_pagination_and_per_page_selector()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('reports.index', ['per_page' => 10]));
        $response->assertStatus(200);
        $response->assertSee('per page');
        $response->assertSee('value="10" selected', false);
    }

    /**
     * 6. Artisan command po:regenerate-docs successfully executes and generates docx files.
     */
    public function test_artisan_regenerate_po_docs_command()
    {
        $invoice = Invoice::create([
            'po_number'    => 'PO-CLI-' . rand(1000, 9999),
            'invoice_number' => rand(100000, 999999),
            'invoice_date' => '2026-07-25',
            'customer_id'  => $this->customer->id,
            'user_id'      => $this->sp->id,
            'order_type'   => Invoice::ORDER_TYPE_NEW,
            'status'       => Invoice::STATUS_APPROVED,
            'amount'       => 35000.00,
            'pending_po_amount' => 35000.00,
        ]);

        $exitCode = \Illuminate\Support\Facades\Artisan::call('po:regenerate-docs', [
            '--id' => $invoice->id,
        ]);

        $this->assertEquals(0, $exitCode);
        $invoice->refresh();
        $this->assertNotNull($invoice->po_document_path);
        $this->assertTrue(app(PoDocumentService::class)->exists($invoice));
    }

    /**
     * 7. PO report pagination strictly caps records at per_page and does not dump opening balances on page 1.
     */
    public function test_po_report_pagination_strictly_caps_per_page()
    {
        $this->actingAs($this->admin);

        // Create 25 opening balances
        for ($i = 0; $i < 25; $i++) {
            $cust = Customer::create([
                'name'        => 'Paging Cust ' . uniqid(),
                'school_code' => 'PAG-' . uniqid(),
            ]);
            SchoolOpeningBalance::create([
                'customer_id'         => $cust->id,
                'user_id'             => $this->sp->id,
                'session_year'        => 2026,
                'opening_amount'      => 1000.00,
                'opening_billed'      => 1000.00,
                'opening_collected'   => 0.00,
                'opening_outstanding' => 1000.00,
                'source'              => 'manual',
                'status'              => 'carry_forward',
                'entered_by'          => $this->admin->id,
            ]);
        }

        // Create 5 POs
        for ($i = 0; $i < 5; $i++) {
            Invoice::create([
                'po_number'         => 'PO-PAGE-' . uniqid(),
                'invoice_number'    => rand(100000, 999999),
                'invoice_date'      => '2026-10-15',
                'customer_id'       => $this->customer->id,
                'user_id'           => $this->sp->id,
                'status'            => Invoice::STATUS_APPROVED,
                'amount'            => 5000.00,
                'pending_po_amount' => 5000.00,
            ]);
        }

        // Request page 1 with per_page = 10
        $response = $this->get(route('reports.index', ['per_page' => 10, 'page' => 1]));
        $response->assertStatus(200);

        $viewRows = $response->viewData('rows');
        // Total items on page 1 MUST NOT exceed perPage (10)
        $this->assertLessThanOrEqual(10, $viewRows->count());
        $this->assertEquals(10, $viewRows->perPage());
        $this->assertEquals(1, $viewRows->currentPage());
    }

    /**
     * 8. Sales person filter with All Sessions shows full amount including pre-July orders (> 20 Lakhs).
     */
    public function test_sales_person_filter_with_all_sessions_shows_full_amount()
    {
        $this->actingAs($this->admin);

        // Pre-July order (April 2026): ₹2,000,000 (20 Lacs)
        Invoice::create([
            'po_number'         => 'PO-20LAC-' . uniqid(),
            'invoice_number'    => rand(100000, 999999),
            'invoice_date'      => '2026-04-10',
            'customer_id'       => $this->customer->id,
            'user_id'           => $this->sp->id,
            'status'            => Invoice::STATUS_APPROVED,
            'amount'            => 2000000.00,
            'pending_po_amount' => 2000000.00,
        ]);

        // Post-July order (August 2026): ₹15,000
        $inv2 = Invoice::create([
            'po_number'         => 'PO-15K-' . uniqid(),
            'invoice_number'    => rand(100000, 999999),
            'invoice_date'      => '2026-08-10',
            'customer_id'       => $this->customer->id,
            'user_id'           => $this->sp->id,
            'status'            => Invoice::STATUS_APPROVED,
            'amount'            => 15000.00,
            'pending_po_amount' => 15000.00,
        ]);

        // When filtering by SP without restricting session (or session=all)
        $response = $this->get(route('reports.index', [
            'sp_id'   => $this->sp->id,
            'session' => 'all',
        ]));

        $response->assertStatus(200);
        $totals = $response->viewData('totals');

        // Total PO amount must include the 20 Lacs pre-July order + 15k post-July order = ₹2,015,000.00
        $this->assertGreaterThanOrEqual(2015000.00, $totals['po_amount']);
        $response->assertSee('2,015,000.00');
    }

    /**
     * 9. Record type filter separates POs and Opening Balances.
     */
    public function test_record_type_filter_separates_po_and_opening_balances()
    {
        $this->actingAs($this->admin);

        // Request record_type = po
        $poResponse = $this->get(route('reports.index', ['record_type' => 'po']));
        $poResponse->assertStatus(200);
        $this->assertEquals('po', $poResponse->viewData('recordType'));
        foreach ($poResponse->viewData('rows') as $row) {
            $this->assertInstanceOf(Invoice::class, $row);
        }

        // Request record_type = opening_balance
        $obResponse = $this->get(route('reports.index', ['record_type' => 'opening_balance']));
        $obResponse->assertStatus(200);
        $this->assertEquals('opening_balance', $obResponse->viewData('recordType'));
        foreach ($obResponse->viewData('rows') as $row) {
            $this->assertInstanceOf(SchoolOpeningBalance::class, $row);
        }
    }
}
