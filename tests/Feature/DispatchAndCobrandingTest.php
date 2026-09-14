<?php

namespace Tests\Feature;

use App\Exports\PurchaseOrdersExport;
use App\Exports\WarehouseDispatchExport;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DispatchAndCobrandingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_purchase_order_co_branding_and_exports()
    {
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $admin = User::create([
            'username' => 'test_adm_' . uniqid(),
            'emp_code' => 'ADM_' . uniqid(),
            'email'    => 'adm_' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
            'role_id'  => $adminRole->id,
        ]);

        $customer = Customer::create([
            'school_code'  => 'SCH-' . rand(1000, 9999),
            'name'         => 'Test School',
            'phone_number' => '9876543210',
            'state'        => 'Delhi',
            'city'         => 'Delhi',
            'created_by'   => $admin->id,
        ]);

        $unit = Unit::firstOrCreate(['name' => 'PCS']);
        $category = Category::firstOrCreate(['name' => 'Robotics']);

        $product = Product::create([
            'name'        => 'Robo Kit',
            'price'       => 1000.00,
            'unit_id'     => $unit->id,
            'category_id' => $category->id,
        ]);

        \Illuminate\Support\Facades\DB::table('items')->updateOrInsert(
            ['id' => $product->id],
            [
                'company_name' => 'SmartK',
                'name'         => 'Robo Kit',
                'master_id'    => 1,
                'group'        => 'Robotics',
                'category'     => 'Robotics',
                'hsn_code'     => '9503',
                'tax_rate'     => 18,
            ]
        );

        // 1. Create PO with co_branding = 1
        $invoice = Invoice::create([
            'po_number'       => 'PO-COB-' . rand(1000, 9999),
            'invoice_date'    => now(),
            'user_id'         => $admin->id,
            'customer_id'     => $customer->id,
            'status'          => Invoice::STATUS_APPROVED,
            'co_branding'     => 1,
            'amount'          => 5000.00,
            'billing_amount'  => 0,
            'collected_amount'=> 0,
        ]);

        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'quantity'   => 5,
            'unit_id'    => $unit->id,
            'rate'       => 1000.00,
            'discount'   => 0,
            'amount'     => 5000.00,
        ]);

        // Check PO export
        $poExport = new PurchaseOrdersExport([], $admin);
        $poHeadings = $poExport->headings();
        $this->assertContains('Co-branding', $poHeadings);

        $invoice->load([
            'user.reportiveTo',
            'customer.leadSource',
            'invoiceItems.product.category',
            'invoiceItems.unit',
            'dispatches.items',
        ]);
        $poMapped = $poExport->map($invoice);
        $this->assertContains('Yes', $poMapped[0]);

        // 2. Record Dispatch with boxes & weight
        $dispatch = Dispatch::create([
            'invoice_id'      => $invoice->id,
            'dispatch_number' => Dispatch::generateNumber(),
            'dispatch_date'   => now(),
            'no_of_boxes'     => 3,
            'total_weight'    => 15.50,
            'dispatched_by'   => $admin->id,
        ]);

        DispatchItem::create([
            'dispatch_id'         => $dispatch->id,
            'invoice_item_id'     => $item->id,
            'product_id'          => $product->id,
            'quantity_dispatched' => 2,
        ]);

        // 3. Test WarehouseDispatchExport (pending vs all)
        $pendingExport = new WarehouseDispatchExport(['scope' => 'pending']);
        $pendingHeadings = $pendingExport->headings();
        $this->assertContains('Co-branding', $pendingHeadings);
        $this->assertContains('No. of Boxes', $pendingHeadings);
        $this->assertContains('Total Weight (kg)', $pendingHeadings);

        $pendingCollection = $pendingExport->collection();
        $matchingPending = $pendingCollection->firstWhere('po_number', $invoice->po_number);
        $this->assertNotNull($matchingPending);
        $this->assertEquals('Yes', $matchingPending['co_branding']);
        $this->assertEquals(3, $matchingPending['no_of_boxes']);
        $this->assertEquals(15.50, $matchingPending['total_weight']);
        $this->assertEquals(2, $matchingPending['dispatched']);
        $this->assertEquals(3, $matchingPending['pending']);

        // Dispatch remaining 3 units
        $dispatch2 = Dispatch::create([
            'invoice_id'      => $invoice->id,
            'dispatch_number' => Dispatch::generateNumber(),
            'dispatch_date'   => now(),
            'no_of_boxes'     => 4,
            'total_weight'    => 20.00,
            'dispatched_by'   => $admin->id,
        ]);

        DispatchItem::create([
            'dispatch_id'         => $dispatch2->id,
            'invoice_item_id'     => $item->id,
            'product_id'          => $product->id,
            'quantity_dispatched' => 3,
        ]);

        // Now with scope = pending, fully dispatched item should NOT appear
        $pendingExport2 = new WarehouseDispatchExport(['scope' => 'pending']);
        $pendingCollection2 = $pendingExport2->collection();
        $this->assertNull($pendingCollection2->firstWhere('po_number', $invoice->po_number));

        // But with scope = all, it SHOULD appear
        $allExport = new WarehouseDispatchExport(['scope' => 'all']);
        $allCollection = $allExport->collection();
        $matchingAll = $allCollection->firstWhere('po_number', $invoice->po_number);
        $this->assertNotNull($matchingAll);
        $this->assertEquals(5, $matchingAll['dispatched']);
        $this->assertEquals(0, $matchingAll['pending']);
        $this->assertEquals(4, $matchingAll['no_of_boxes']);
        $this->assertEquals(20.00, $matchingAll['total_weight']);
    }
}
