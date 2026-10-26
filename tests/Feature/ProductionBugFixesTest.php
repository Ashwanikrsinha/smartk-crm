<?php

namespace Tests\Feature;

use App\Models\BillingEntry;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\OpeningBalanceCollection;
use App\Models\Product;
use App\Models\Role;
use App\Models\SchoolOpeningBalance;
use App\Models\Target;
use App\Models\Unit;
use App\Models\User;
use App\Services\PoDocumentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class ProductionBugFixesTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $accountsUser;
    protected User $spUser;
    protected Customer $customer;
    protected Category $category;
    protected Unit $unit;
    protected Product $product1;
    protected Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $accountsRole = Role::firstOrCreate(['name' => 'Accounts']);
        $spRole = Role::firstOrCreate(['name' => 'Sales Person']);

        $this->admin = User::create([
            'username' => 'test_adm_' . uniqid(),
            'emp_code' => 'ADM_' . uniqid(),
            'email'    => 'adm_' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
            'role_id'  => $adminRole->id,
        ]);

        $this->accountsUser = User::create([
            'username' => 'test_acc_' . uniqid(),
            'emp_code' => 'ACC_' . uniqid(),
            'email'    => 'acc_' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
            'role_id'  => $accountsRole->id,
        ]);

        $this->spUser = User::create([
            'username' => 'test_sp_' . uniqid(),
            'emp_code' => 'SP_' . uniqid(),
            'email'    => 'sp_' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
            'role_id'  => $spRole->id,
        ]);

        $this->customer = Customer::create([
            'name'        => 'Test BugFix School ' . rand(100, 999),
            'school_code' => 'SCH-BUG-' . rand(100, 999),
            'address'     => '123 Main Road',
            'city'        => 'Delhi',
            'state'       => 'Delhi',
            'pin_code'    => '110001',
        ]);

        $this->category = Category::firstOrCreate(['name' => 'Core Curriculum']);
        $this->unit     = Unit::firstOrCreate(['name' => 'Kit']);

        $this->product1 = Product::create([
            'name'        => 'Kit Level 1 ' . rand(100, 999),
            'category_id' => $this->category->id,
            'price'       => 2874.00,
        ]);

        $this->product2 = Product::create([
            'name'        => 'Kit Level 2 ' . rand(100, 999),
            'category_id' => $this->category->id,
            'price'       => 3100.00,
        ]);
    }

    /**
     * Bug 1: View composer safely resolves company without crashing if DB fails or uninitialized.
     */
    public function test_bug1_app_service_provider_safe_company_view_sharing()
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $this->assertArrayHasKey('bearer_token', View::getShared());
        $this->assertArrayHasKey('company', View::getShared());
    }

    /**
     * Bug 2: saveSingle saves pure billing entries with reference_number even if payment_mode is omitted.
     */
    public function test_bug2_save_single_billing_with_reference_number_and_no_payment_mode()
    {
        $this->actingAs($this->accountsUser);

        $invoice = Invoice::create([
            'po_number'         => 'PO-B2-' . rand(1000, 9999),
            'invoice_number'    => rand(100000, 999999999),
            'invoice_date'      => now()->toDateString(),
            'user_id'           => $this->spUser->id,
            'customer_id'       => $this->customer->id,
            'status'            => Invoice::STATUS_APPROVED,
            'amount'            => 15000.00,
            'billing_amount'    => 0,
            'gst_amount'        => 0,
            'collected_amount'  => 0,
            'outstanding_amount'=> 0,
            'pending_po_amount' => 15000.00,
        ]);

        // Submit pure billing entry with bill ref no, no payment_mode, no collection
        $response = $this->postJson(route('collections.save-single'), [
            'invoice_id'        => $invoice->id,
            'billed_amount'     => 6236.00,
            'gst_amount'        => 137.29,
            'collected_amount'  => 0,
            'payment_mode'      => null, // omitted
            'billing_source'    => 'manual',
            'billing_reference' => 'SCE/0499/26-27',
            'entry_date'        => now()->toDateString(),
            'remarks'           => 'Tally sync',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'        => true,
            'billing_amount' => 6236.00,
            'gst_amount'     => 137.29,
        ]);

        $invoice->refresh();
        $this->assertEquals(6236.00, (float) $invoice->billing_amount);
        $this->assertEquals(137.29, (float) $invoice->gst_amount);

        // Verify billing entry was recorded with reference_number
        $this->assertDatabaseHas('billing_entries', [
            'invoice_id'       => $invoice->id,
            'billed_amount'    => 6236.00,
            'reference_number' => 'SCE/0499/26-27',
        ]);
    }

    /**
     * Bug 3: Bulk store does not overwrite GST to 0 when input is empty.
     */
    public function test_bug3_bulk_store_preserves_gst_when_gst_input_is_blank()
    {
        $this->actingAs($this->accountsUser);

        $invoice = Invoice::create([
            'po_number'         => 'PO-B3-' . rand(1000, 9999),
            'invoice_number'    => rand(100000, 999999999),
            'invoice_date'      => now()->toDateString(),
            'user_id'           => $this->spUser->id,
            'customer_id'       => $this->customer->id,
            'status'            => Invoice::STATUS_APPROVED,
            'amount'            => 20000.00,
            'billing_amount'    => 10000.00,
            'gst_amount'        => 1800.00,
            'collected_amount'  => 5000.00,
            'outstanding_amount'=> 5000.00,
            'pending_po_amount' => 10000.00,
        ]);

        Collection::create([
            'invoice_id'       => $invoice->id,
            'collected_amount' => 5000.00,
            'payment_mode'     => 'cheque',
            'collected_by'     => $this->admin->id,
            'collected_at'     => now()->toDateString(),
        ]);

        // Submit bulk update where gst_amount is null or empty string
        $response = $this->post(route('collections.bulk-store'), [
            'collections' => [
                [
                    'invoice_id'       => $invoice->id,
                    'billed_amount'    => 0,
                    'gst_amount'       => '', // Blank - should not overwrite
                    'collected_amount' => 2000.00,
                ]
            ],
            'payment_mode' => 'cheque',
            'collected_at' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('collections.index'));

        $invoice->refresh();
        // GST remains 1800.00, not wiped to 0.00!
        $this->assertEquals(1800.00, (float) $invoice->gst_amount);
        $this->assertEquals(7000.00, (float) $invoice->collected_amount);
    }

    /**
     * Bug 4: Total collected and monthlyTrend include Opening Balance collections.
     */
    public function test_bug4_opening_balance_collection_included_in_total_collected_and_trend()
    {
        $this->actingAs($this->admin);

        $sessionYear = SchoolOpeningBalance::currentSessionYear();

        $ob = SchoolOpeningBalance::create([
            'customer_id'         => $this->customer->id,
            'user_id'             => $this->spUser->id,
            'session_year'        => $sessionYear,
            'opening_amount'      => 100000.00,
            'opening_billed'      => 100000.00,
            'opening_collected'   => 0.00,
            'opening_outstanding' => 100000.00,
            'status'              => 'carry_forward',
        ]);

        // Record 40,000 collection against opening balance
        OpeningBalanceCollection::create([
            'opening_balance_id' => $ob->id,
            'collected_amount'   => 40000.00,
            'payment_mode'       => 'neft',
            'collected_by'       => $this->admin->id,
            'collected_at'       => now()->toDateString(),
        ]);

        $ob->refresh();
        $this->assertEquals(40000.00, (float) $ob->opening_collected);
        $this->assertEquals(60000.00, (float) $ob->opening_outstanding);

        // Check Collections index page totals include OB collection
        $response = $this->get(route('collections.index', [
            'sp_id' => $this->spUser->id,
            'year'  => $sessionYear,
        ]));
        $response->assertStatus(200);
        $totals = $response->viewData('totals');
        $this->assertGreaterThanOrEqual(40000.00, $totals['collected']);

        // Check Admin Dashboard totals include OB collection
        $dashResponse = $this->get(route('dashboard'));
        $dashResponse->assertStatus(200);
        $totalCollected = $dashResponse->viewData('totalCollection');
        $this->assertGreaterThanOrEqual(40000.00, $totalCollected);
    }

    /**
     * Bug 5: Target achieve calculation is based on billed_at date (date of billing), NOT invoice_date.
     */
    public function test_bug5_target_achieved_calculates_from_billed_at_date()
    {
        $this->actingAs($this->admin);

        $month = 9;  // September
        $year  = 2026;

        $target = Target::create([
            'user_id'       => $this->spUser->id,
            'target_amount' => 50000.00,
            'month'         => $month,
            'year'          => $year,
        ]);

        // Invoice was created in May 2026 (invoice_date = 2026-05-10)
        $invoice = Invoice::create([
            'po_number'         => 'PO-TGT-' . rand(1000, 9999),
            'invoice_number'    => rand(100000, 999999999),
            'invoice_date'      => '2026-05-10',
            'user_id'           => $this->spUser->id,
            'customer_id'       => $this->customer->id,
            'status'            => Invoice::STATUS_APPROVED,
            'amount'            => 60000.00,
            'billing_amount'    => 50000.00,
            'gst_amount'        => 0.00,
            'collected_amount'  => 0,
            'outstanding_amount'=> 50000.00,
            'pending_po_amount' => 10000.00,
        ]);

        // Billing entry happened in SEPTEMBER 2026 (billed_at = 2026-09-15)
        BillingEntry::create([
            'invoice_id'       => $invoice->id,
            'billed_amount'    => 50000.00,
            'source'           => BillingEntry::SOURCE_MANUAL,
            'reference_number' => 'BILL-SEP-001',
            'entered_by'       => $this->admin->id,
            'billed_at'        => '2026-09-15',
        ]);

        // September target achieved should count the 50,000 billing that took place in September!
        $this->assertEquals(50000.00, $target->achievedAmount());
        $this->assertEquals(100, $target->achievementPercent());

        // A May target for this user should NOT count this billing
        $mayTarget = Target::create([
            'user_id'       => $this->spUser->id,
            'target_amount' => 50000.00,
            'month'         => 5,
            'year'          => $year,
        ]);
        $this->assertEquals(0.00, $mayTarget->achievedAmount());
    }

    /**
     * Bug 6: PoDocumentService uses the invoice's original invoice_date, not approved_at.
     */
    public function test_bug6_po_document_uses_invoice_date_not_approval_date()
    {
        $invoice = Invoice::create([
            'po_number'         => 'PO-DATE-' . rand(1000, 9999),
            'invoice_number'    => rand(100000, 999999999),
            'invoice_date'      => '2026-04-12',
            'user_id'           => $this->spUser->id,
            'customer_id'       => $this->customer->id,
            'status'            => Invoice::STATUS_APPROVED,
            'approved_at'       => '2026-10-22 10:00:00', // approved much later
            'amount'            => 10000.00,
            'billing_amount'    => 0,
            'gst_amount'        => 0,
            'collected_amount'  => 0,
            'outstanding_amount'=> 0,
            'pending_po_amount' => 10000.00,
        ]);

        InvoiceItem::create([
            'invoice_id'  => $invoice->id,
            'product_id'  => $this->product1->id,
            'unit_id'     => $this->unit->id,
            'quantity'    => 1,
            'rate'        => 10000.00,
            'discount'    => 0,
            'amount'      => 10000.00,
        ]);

        $service = app(PoDocumentService::class);
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('buildData');
        $method->setAccessible(true);
        $payload = $method->invoke($service, $invoice);

        // Must show original PO date: 12 April, 2026 (NOT 22 October, 2026)
        $this->assertEquals('12 April, 2026', $payload['po_date']);
    }

    /**
     * Bug 7: PO created with 4-5 line items saves all items without dropping any row.
     */
    public function test_bug7_create_po_with_multiple_items_saves_all_and_matches_total()
    {
        $this->actingAs($this->admin);

        $prod3 = Product::create([
            'name'        => 'Kit Level 3 ' . rand(100, 999),
            'category_id' => $this->category->id,
            'price'       => 3325.00,
        ]);
        $prod4 = Product::create([
            'name'        => 'Kit Readiness ' . rand(100, 999),
            'category_id' => $this->category->id,
            'price'       => 2874.00,
        ]);

        // Submit 4 line items
        $response = $this->post(route('invoices.store'), [
            'action'            => 'submit',
            'invoice_date'      => now()->toDateString(),
            'delivery_due_date' => now()->addDays(30)->toDateString(),
            'customer_id'       => $this->customer->id,
            'phone_number'      => '9876543210',
            'contact_person'    => 'John Doe',
            'address'           => 'School Campus',
            'co_branding'       => '1',
            'order_type'        => Invoice::ORDER_TYPE_NEW,
            'terms'             => 'Standard terms',
            'total_po_amount'   => 173068.00,
            'categories'        => [
                0 => $this->category->id,
                1 => $this->category->id,
                2 => $this->category->id,
                3 => $this->category->id,
            ],
            'products'          => [
                0 => $this->product1->id, // Level 1
                1 => $this->product2->id, // Level 2
                2 => $prod3->id,          // Level 3
                3 => $prod4->id,          // Readiness
            ],
            'mrps'              => [0 => 2874, 1 => 3100, 2 => 3325, 3 => 2874],
            'quantities'        => [0 => 12, 1 => 12, 2 => 12, 3 => 12],
            'discounts'         => [0 => 0, 1 => 0, 2 => 0, 3 => 0],
            'rates'             => [0 => 2874, 1 => 3100, 2 => 3325, 3 => 2874],
            'amounts'           => [
                0 => 34488.00,
                1 => 37200.00,
                2 => 39900.00,
                3 => 34488.00,
            ],
            'units'             => [0 => $this->unit->id, 1 => $this->unit->id, 2 => $this->unit->id, 3 => $this->unit->id],
            'descriptions'      => [0 => 'Level 1 item', 1 => 'Level 2 item', 2 => 'Level 3 item', 3 => 'Readiness item'],
        ]);

        $response->assertSessionHasNoErrors();
        $invoice = Invoice::where('customer_id', $this->customer->id)->latest('id')->first();
        $this->assertNotNull($invoice);

        // Verify ALL 4 items are saved in the database
        $this->assertCount(4, $invoice->invoiceItems);

        // Verify each product is present
        $savedProductIds = $invoice->invoiceItems->pluck('product_id')->toArray();
        $this->assertContains($this->product1->id, $savedProductIds);
        $this->assertContains($this->product2->id, $savedProductIds);
        $this->assertContains($prod3->id, $savedProductIds);
        $this->assertContains($prod4->id, $savedProductIds);

        // Verify total matches items sum
        $itemsSum = (float) $invoice->invoiceItems->sum('amount');
        $this->assertEquals($itemsSum, (float) $invoice->amount);
    }
}
