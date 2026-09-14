<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Dispatch;
use App\Models\Invoice;
use App\Models\Role;
use App\Models\Target;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PodSlipAndTargetTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $this->admin = User::create([
            'username' => 'test_admin_' . uniqid(),
            'emp_code' => 'ADM_' . uniqid(),
            'email'    => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
            'role_id'  => $adminRole->id,
        ]);

        $this->customer = Customer::create([
            'school_code'  => 'SCH-' . rand(1000, 9999),
            'name'         => 'Test School',
            'phone_number' => '9876543210',
            'state'        => 'Maharashtra',
            'city'         => 'Mumbai',
            'created_by'   => $this->admin->id,
        ]);
    }

    public function test_dispatch_pod_slip_upload_and_view()
    {
        $this->actingAs($this->admin);

        $invoice = Invoice::create([
            'po_number'       => 'PO-POD-' . rand(1000, 9999),
            'invoice_number'  => rand(100000, 999999999),
            'invoice_date'    => now(),
            'user_id'         => $this->admin->id,
            'customer_id'     => $this->customer->id,
            'status'          => Invoice::STATUS_APPROVED,
            'amount'          => 10000.00,
            'billing_amount'  => 0,
            'collected_amount'=> 0,
        ]);

        $file = UploadedFile::fake()->create('lr_bilty.pdf', 200, 'application/pdf');

        $dispatch = Dispatch::create([
            'invoice_id'      => $invoice->id,
            'dispatch_number' => Dispatch::generateNumber(),
            'dispatch_date'   => now(),
            'no_of_boxes'     => 5,
            'total_weight'    => 50.00,
            'bilty_number'    => 'LR-12345',
            'pod_slip'        => $file->store('dispatches/pod', 'public'),
            'dispatched_by'   => $this->admin->id,
        ]);

        $this->assertNotNull($dispatch->pod_slip);
        Storage::disk('public')->assertExists($dispatch->pod_slip);

        // Test view pod route
        $response = $this->get(route('dispatches.pod', $dispatch));
        $response->assertStatus(200);

        // Test update dispatch with a new pod_slip
        $newFile = UploadedFile::fake()->create('new_lr_bilty.jpg', 150, 'image/jpeg');
        $oldPath = $dispatch->pod_slip;

        $updateResponse = $this->patch(route('dispatches.update', $dispatch), [
            'dispatch_date' => now()->format('Y-m-d'),
            'no_of_boxes'   => 6,
            'total_weight'  => 60.00,
            'bilty_number'  => 'LR-99999',
            'pod_slip'      => $newFile,
        ]);

        $updateResponse->assertRedirect();
        $dispatch->refresh();
        $this->assertNotEquals($oldPath, $dispatch->pod_slip);
        Storage::disk('public')->assertExists($dispatch->pod_slip);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_target_achieved_amount_calculates_net_billed()
    {
        $this->actingAs($this->admin);

        $month = 10;
        $year = 2026;

        $target = Target::create([
            'user_id'       => $this->admin->id,
            'target_amount' => 100000.00,
            'month'         => $month,
            'year'          => $year,
        ]);

        // Invoice in October 2026: PO amount = 60000, billing_amount = 50000, gst_amount = 9000
        // Net Billed = 50000 - 9000 = 41000
        Invoice::create([
            'po_number'       => 'PO-NET-' . rand(1000, 9999),
            'invoice_number'  => rand(100000, 999999999),
            'invoice_date'    => "{$year}-10-15",
            'user_id'         => $this->admin->id,
            'customer_id'     => $this->customer->id,
            'status'          => Invoice::STATUS_APPROVED,
            'amount'          => 60000.00,
            'billing_amount'  => 50000.00,
            'gst_amount'      => 9000.00,
            'collected_amount'=> 0,
        ]);

        // Another invoice in October 2026: PO amount = 20000, billing_amount = 20000, gst_amount = 0
        // Net Billed = 20000
        Invoice::create([
            'po_number'       => 'PO-NET2-' . rand(1000, 9999),
            'invoice_number'  => rand(100000, 999999999),
            'invoice_date'    => "{$year}-10-20",
            'user_id'         => $this->admin->id,
            'customer_id'     => $this->customer->id,
            'status'          => Invoice::STATUS_APPROVED,
            'amount'          => 20000.00,
            'billing_amount'  => 20000.00,
            'gst_amount'      => 0.00,
            'collected_amount'=> 0,
        ]);

        // Invoice in another month (September 2026) - should not be included
        Invoice::create([
            'po_number'       => 'PO-SEP-' . rand(1000, 9999),
            'invoice_number'  => rand(100000, 999999999),
            'invoice_date'    => "{$year}-09-10",
            'user_id'         => $this->admin->id,
            'customer_id'     => $this->customer->id,
            'status'          => Invoice::STATUS_APPROVED,
            'amount'          => 30000.00,
            'billing_amount'  => 30000.00,
            'gst_amount'      => 0.00,
            'collected_amount'=> 0,
        ]);

        // Expected achieved for Oct 2026 = 41000 + 20000 = 61000.00
        $this->assertEquals(61000.00, $target->achievedAmount());
        // Percentage = 61%
        $this->assertEquals(61, $target->achievementPercent());

        // Test TargetController index AJAX response
        $ajaxResponse = $this->getJson(route('targets.index', ['user_id' => $this->admin->id]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $ajaxResponse->assertStatus(200);
        $data = $ajaxResponse->json();

        $this->assertArrayHasKey('total_target', $data);
        $this->assertArrayHasKey('total_achieved', $data);
        $this->assertStringContainsString('61,000.00', $data['total_achieved']);
    }

    public function test_dispatches_index_page_and_export_contain_pod_slip()
    {
        $this->actingAs($this->admin);

        $invoice = Invoice::create([
            'po_number'       => 'PO-PAGE-' . rand(1000, 9999),
            'invoice_number'  => rand(100000, 999999999),
            'invoice_date'    => now(),
            'user_id'         => $this->admin->id,
            'customer_id'     => $this->customer->id,
            'status'          => Invoice::STATUS_APPROVED,
            'amount'          => 5000.00,
            'billing_amount'  => 0,
            'collected_amount'=> 0,
        ]);

        $unit = \App\Models\Unit::firstOrCreate(['name' => 'PCS']);
        $product = \App\Models\Product::firstOrCreate(
            ['name' => 'Test Product'],
            ['price' => 500, 'unit_id' => $unit->id, 'category_id' => 1]
        );

        \Illuminate\Support\Facades\DB::table('items')->updateOrInsert(
            ['id' => $product->id],
            [
                'company_name' => 'SmartK',
                'name'         => 'Test Product',
                'master_id'    => 1,
                'group'        => 'Test',
                'category'     => 'Test',
                'hsn_code'     => '1234',
                'tax_rate'     => 18,
            ]
        );

        $item = \App\Models\InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'quantity'   => 10,
            'unit_id'    => $unit->id,
            'rate'       => 500,
            'amount'     => 5000,
        ]);

        $file = UploadedFile::fake()->create('slip.pdf', 100, 'application/pdf');

        $dispatch = Dispatch::create([
            'invoice_id'      => $invoice->id,
            'dispatch_number' => Dispatch::generateNumber(),
            'dispatch_date'   => now(),
            'no_of_boxes'     => 2,
            'total_weight'    => 10.00,
            'bilty_number'    => 'LR-PAGE-123',
            'pod_slip'        => $file->store('dispatches/pod', 'public'),
            'dispatched_by'   => $this->admin->id,
        ]);

        \App\Models\DispatchItem::create([
            'dispatch_id'         => $dispatch->id,
            'invoice_item_id'     => $item->id,
            'product_id'          => $product->id,
            'quantity_dispatched' => 2,
        ]);

        // Check dispatch index page has POD Slip table column and View POD button
        $response = $this->get(route('dispatches.index'));
        $response->assertStatus(200);
        $response->assertSee('POD Slip');
        $response->assertSee('View POD');
        $response->assertSee(route('dispatches.pod', $dispatch));
        $response->assertSee('Export Pending');
        $response->assertSee('Export All');

        // Check PO export includes Bilty Number and POD Slip
        $poExport = new \App\Exports\PurchaseOrdersExport([], $this->admin);
        $this->assertContains('Bilty Number', $poExport->headings());
        $this->assertContains('POD Slip', $poExport->headings());

        // Check Warehouse Dispatch export includes POD Slip
        $whExport = new \App\Exports\WarehouseDispatchExport(['scope' => 'all']);
        $this->assertContains('POD Slip', $whExport->headings());
    }

    public function test_sales_manager_dashboard_target_section_and_team_dropdown()
    {
        $smRole = Role::firstOrCreate(['name' => 'Manager']);
        $spRole = Role::firstOrCreate(['name' => 'Operator']);

        $perm = \App\Models\Permission::firstOrCreate(['name' => 'view_team_data']);
        $smRole->permissions()->syncWithoutDetaching([$perm->id]);

        $salesManager = User::create([
            'username' => 'sales_mgr_' . uniqid(),
            'emp_code' => 'SM_' . uniqid(),
            'email'    => 'sm_' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
            'role_id'  => $smRole->id,
        ]);

        $salesPerson = User::create([
            'username'     => 'sales_rep_' . uniqid(),
            'emp_code'     => 'SP_' . uniqid(),
            'email'        => 'sp_' . uniqid() . '@example.com',
            'password'     => bcrypt('secret'),
            'role_id'      => $spRole->id,
            'reportive_id' => $salesManager->id,
        ]);

        $month = (int) date('m');
        $year  = (int) date('Y');

        Target::create([
            'user_id'       => $salesPerson->id,
            'target_amount' => 50000.00,
            'month'         => $month,
            'year'          => $year,
        ]);

        Target::create([
            'user_id'       => $salesManager->id,
            'target_amount' => 50000.00,
            'month'         => $month,
            'year'          => $year,
        ]);

        Invoice::create([
            'po_number'       => 'PO-SM-' . rand(1000, 9999),
            'invoice_number'  => rand(100000, 999999999),
            'invoice_date'    => now(),
            'user_id'         => $salesPerson->id,
            'customer_id'     => $this->customer->id,
            'status'          => Invoice::STATUS_APPROVED,
            'amount'          => 30000.00,
            'billing_amount'  => 30000.00,
            'gst_amount'      => 0.00,
            'collected_amount'=> 0,
        ]);

        $this->actingAs($salesManager);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);

        // Verify target section rendered
        $response->assertSee('Target vs Achievement');
        $response->assertSee('Target Amount');
        $response->assertSee('Achieved (Net Billed)');
        $response->assertSee('Achieved Percentage');
        $response->assertSee('100,000.00'); // Total target (50k + 50k)
        $response->assertSee('30,000.00');  // Achieved 30k
        $response->assertSee('30%');        // 30%

        // Verify Sales Manager is included in Team Member dropdown
        $response->assertSee($salesManager->username . ' (Me)');
        $response->assertSee($salesPerson->username);
    }
}

