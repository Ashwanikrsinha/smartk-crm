<?php

namespace Tests\Feature;

use App\Exports\PurchaseOrdersExport;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SchoolOpeningBalance;
use App\Models\PoLog;
use App\Models\Product;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PoEnhancementsAndCollectionsTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private Customer $customer;
    private Product $product;
    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $this->admin = User::create([
            'username' => 'admin_' . uniqid(),
            'emp_code' => 'ADM_' . uniqid(),
            'email'    => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $adminRole->id,
        ]);

        $this->customer = Customer::create([
            'school_code'  => 'SCH-' . rand(10000, 99999),
            'name'         => 'Oakwood International',
            'phone_number' => '9876543210',
            'state'        => 'Delhi',
            'city'         => 'Delhi',
            'created_by'   => $this->admin->id,
        ]);

        $this->unit = Unit::firstOrCreate(['name' => 'PCS']);
        $category = Category::firstOrCreate(['name' => 'Science']);

        $this->product = Product::create([
            'name'        => 'STEM Lab Kit',
            'price'       => 1500.00,
            'unit_id'     => $this->unit->id,
            'category_id' => $category->id,
        ]);

        \Illuminate\Support\Facades\DB::table('items')->updateOrInsert(
            ['id' => $this->product->id],
            [
                'company_name' => 'SmartK',
                'name'         => 'STEM Lab Kit',
                'master_id'    => 1,
                'group'        => 'Science',
                'category'     => 'Science',
                'hsn_code'     => '9503',
                'tax_rate'     => 18,
            ]
        );
    }

    public function test_get_school_returns_contact_person_and_po_saves_it()
    {
        CustomerContact::create([
            'customer_id'    => $this->customer->id,
            'name'           => 'Dr. Robert Principal',
            'contact_number' => '9876543210',
        ]);

        $this->actingAs($this->admin);
        $this->withoutExceptionHandling();

        // Test AJAX /invoices/school/{customer}
        $res = $this->getJson(route('invoices.school', $this->customer));
        $res->assertOk();
        $res->assertJsonFragment([
            'contact_person' => 'Dr. Robert Principal',
        ]);

        // Create PO with order_type and contact_person
        $postData = [
            'customer_id'     => $this->customer->id,
            'invoice_date'    => now()->format('Y-m-d'),
            'academic_session'=> '2026-27',
            'order_type'      => Invoice::ORDER_TYPE_RENEWAL,
            'contact_person'  => 'Dr. Robert Principal',
            'co_branding'     => '1',
            'action'          => 'draft',
            'total_po_amount' => 3000.00,
            'products'        => [$this->product->id],
            'units'           => [$this->unit->id],
            'mrps'            => [1500.00],
            'quantities'      => [2],
            'rates'           => [1500.00],
            'discounts'       => [0],
            'amounts'         => [3000.00],
        ];

        $resp = $this->post(route('invoices.store'), $postData);
        $resp->assertSessionHasNoErrors();
        $resp->assertRedirect();

        $invoice = Invoice::where('customer_id', $this->customer->id)->latest('id')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('Renewal', $invoice->order_type);
        $this->assertEquals('Dr. Robert Principal', $invoice->contact_person);
        $this->assertEquals(1, $invoice->co_branding);

        // View show page
        $viewResp = $this->get(route('invoices.show', $invoice));
        $viewResp->assertOk();
        $viewResp->assertSee('Renewal');
        $viewResp->assertSee('Dr. Robert Principal');
    }

    public function test_order_type_validation_fails_on_invalid_option()
    {
        $this->actingAs($this->admin);

        $postData = [
            'customer_id'    => $this->customer->id,
            'invoice_date'   => now()->format('Y-m-d'),
            'academic_session'=> '2026-27',
            'order_type'     => 'InvalidType',
            'action'         => 'draft',
            'product_id'     => [$this->product->id],
            'quantity'       => [1],
            'rates'          => [1500.00],
            'discount'       => [0],
            'total'          => [1500.00],
            'sub_total'      => 1500.00,
            'total_amount'   => 1500.00,
        ];

        $resp = $this->post(route('invoices.store'), $postData);
        $resp->assertSessionHasErrors(['order_type']);
    }

    public function test_reports_pagination_and_cobranding_display()
    {
        $this->actingAs($this->admin);

        // Create 25 invoices: some with co_branding=1, some with co_branding=0
        for ($i = 1; $i <= 25; $i++) {
            $inv = Invoice::create([
                'invoice_number'  => 50000 + $i,
                'po_number'       => 'PO-PAG-' . rand(100000, 999999),
                'invoice_date'    => now()->subDays($i),
                'user_id'         => $this->admin->id,
                'customer_id'     => $this->customer->id,
                'status'          => Invoice::STATUS_APPROVED,
                'co_branding'     => ($i % 2 === 0) ? 1 : 0,
                'order_type'      => ($i % 3 === 0) ? Invoice::ORDER_TYPE_REORDER : Invoice::ORDER_TYPE_NEW,
                'amount'          => 1000.00,
                'billing_amount'  => 0,
                'collected_amount'=> 0,
                'outstanding_amount' => 0,
            ]);

            InvoiceItem::create([
                'invoice_id' => $inv->id,
                'product_id' => $this->product->id,
                'quantity'   => 1,
                'unit_id'    => $this->unit->id,
                'rate'       => 1000.00,
                'discount'   => 0,
                'amount'     => 1000.00,
            ]);
        }

        // Also add an opening balance
        SchoolOpeningBalance::create([
            'customer_id'       => $this->customer->id,
            'user_id'           => $this->admin->id,
            'session_year'      => (int) date('Y'),
            'opening_amount'    => 50000.00,
            'opening_collected' => 10000.00,
        ]);

        $res = $this->get(route('reports.index'));
        $res->assertOk();

        // Check pagination controls are rendered
        $res->assertSee('pagination');
        $res->assertSee('Showing');

        // Check Order Type header and badges
        $res->assertSee('Order Type');
        $res->assertSee('New On-board');

        // Check co-branding badge for PO
        $res->assertSee('Yes');
        $res->assertSee('No');

        // Page 2 should not have opening balance row
        $resPage2 = $this->get(route('reports.index', ['page' => 2]));
        $resPage2->assertOk();
        $resPage2->assertDontSee('Opening Balance</span>', false);
    }

    public function test_purchase_orders_export_has_order_type_and_opening_balance_blank_cobranding()
    {
        $invoice = Invoice::create([
            'invoice_number'  => 60001,
            'po_number'       => 'PO-EXP-OT-' . rand(10000, 99999),
            'invoice_date'    => now(),
            'user_id'         => $this->admin->id,
            'customer_id'     => $this->customer->id,
            'status'          => Invoice::STATUS_APPROVED,
            'co_branding'     => 1,
            'order_type'      => Invoice::ORDER_TYPE_REORDER,
            'amount'          => 2000.00,
            'billing_amount'  => 0,
            'collected_amount'=> 0,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $this->product->id,
            'quantity'   => 2,
            'unit_id'    => $this->unit->id,
            'rate'       => 1000.00,
            'discount'   => 0,
            'amount'     => 2000.00,
        ]);

        $ob = SchoolOpeningBalance::create([
            'customer_id'       => $this->customer->id,
            'user_id'           => $this->admin->id,
            'session_year'      => (int) date('Y'),
            'opening_amount'    => 25000.00,
            'opening_collected' => 5000.00,
        ]);

        $export = new PurchaseOrdersExport(['session_year' => (int) date('Y')], $this->admin);
        $headings = $export->headings();
        $this->assertContains('Order Type', $headings);
        $this->assertContains('Co-branding', $headings);

        $collection = $export->collection();
        // First row should be the Opening Balance
        $obRow = $collection->first();
        $this->assertEquals('Opening Balance (Carry-Forward)', $obRow[0]);
        $this->assertEquals('', $obRow[2]); // Co-branding blank
        $this->assertEquals('', $obRow[3]); // Order Type blank

        // PO row
        $poRow = $collection->firstWhere(0, $invoice->po_number);
        $this->assertNotNull($poRow);
        $this->assertEquals('Yes', $poRow[2]);
        $this->assertEquals('Re-order', $poRow[3]);
    }

    public function test_save_single_supports_negative_billing_and_collection()
    {
        $this->actingAs($this->admin);

        $invoice = Invoice::create([
            'invoice_number'     => 70001,
            'po_number'          => 'PO-NEG-' . rand(10000, 99999),
            'invoice_date'       => now(),
            'user_id'            => $this->admin->id,
            'customer_id'        => $this->customer->id,
            'status'             => Invoice::STATUS_APPROVED,
            'amount'             => 10000.00,
            'billing_amount'     => 10000.00,
            'collected_amount'   => 5000.00,
            'outstanding_amount' => 5000.00,
        ]);

        // Accounts posts negative billing adjustment (e.g. credit note / return -2000)
        // with entry_date
        $resBilled = $this->postJson(route('collections.save-single'), [
            'invoice_id'        => $invoice->id,
            'billed_amount'     => -2000.00,
            'collected_amount'  => 0,
            'payment_mode'      => 'cheque',
            'entry_date'        => '2026-10-10',
            'billing_reference' => 'CN-001',
            'remarks'           => 'Credit Note Adjustment',
        ]);

        $resBilled->assertOk();
        $resBilled->assertJson([
            'success'        => true,
            'billing_amount' => -2000.00, // recalculated from billingEntries sum
        ]);

        // Verify PoLog recorded entry_date
        $billedLog = PoLog::where('invoice_id', $invoice->id)
            ->where('action', PoLog::ACTION_BILLED)
            ->latest('id')
            ->first();
        $this->assertNotNull($billedLog);
        $this->assertEquals('2026-10-10', $billedLog->entry_date?->format('Y-m-d'));

        // Accounts posts negative collection adjustment (e.g. bounce / refund -1000)
        $resColl = $this->postJson(route('collections.save-single'), [
            'invoice_id'        => $invoice->id,
            'billed_amount'     => 0,
            'collected_amount'  => -1000.00,
            'payment_mode'      => 'cheque',
            'entry_date'        => '2026-10-12',
            'reference_number'  => 'BOUNCE-001',
            'remarks'           => 'Cheque Bounce Reversal',
        ]);

        $resColl->assertOk();
        $resColl->assertJson([
            'success'          => true,
            'collected_amount' => -1000.00,
        ]);

        $collLog = PoLog::where('invoice_id', $invoice->id)
            ->where('action', PoLog::ACTION_COLLECTED)
            ->latest('id')
            ->first();
        $this->assertNotNull($collLog);
        $this->assertEquals('2026-10-12', $collLog->entry_date?->format('Y-m-d'));
    }
}
