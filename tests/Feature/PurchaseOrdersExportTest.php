<?php

namespace Tests\Feature;

use App\Exports\PurchaseOrdersExport;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PurchaseOrdersExportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_purchase_orders_export_headings_and_item_calculations()
    {
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $admin = User::create([
            'username' => 'test_admin_' . uniqid(),
            'emp_code' => 'EMP-ADM-' . uniqid(),
            'email'    => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $adminRole->id,
        ]);

        $customer = Customer::create([
            'school_code'  => 'SCH-EXP-' . rand(1000, 9999),
            'name'         => 'Export School',
            'phone_number' => '9876543210',
            'state'        => 'Maharashtra',
            'city'         => 'Pune',
            'created_by'   => $admin->id,
        ]);

        $unit = Unit::firstOrCreate(['name' => 'PCS']);

        $product = Product::create([
            'name'        => 'Sample Kit',
            'price'       => 950.00,
            'unit_id'     => $unit->id,
            'category_id' => 1,
        ]);

        $invoice = Invoice::create([
            'po_number'       => 'PO-TEST-' . rand(1000, 9999),
            'invoice_date'    => now(),
            'user_id'         => $admin->id,
            'customer_id'     => $customer->id,
            'status'          => 'approved',
            'amount'          => 9025.00,
            'billing_amount'  => 0,
            'collected_amount'=> 0,
        ]);

        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'quantity'   => 10,
            'unit_id'    => $unit->id,
            'rate'       => 902.50,
            'discount'   => 5.00,
            'amount'     => 9025.00,
        ]);

        $invoice->load([
            'user.reportiveTo',
            'customer.leadSource',
            'invoiceItems.product.category',
            'invoiceItems.unit',
            'dispatches.items',
        ]);

        $export = new PurchaseOrdersExport([], $admin);
        $headings = $export->headings();

        // Check headings
        $this->assertContains('Rate (₹)', $headings);
        $this->assertContains('Discount (%)', $headings);
        $this->assertContains('Discount Amount (₹)', $headings);
        $this->assertContains('Net Rate (₹)', $headings);
        $this->assertContains('Item Amount (₹)', $headings);

        $mapped = $export->map($invoice);
        $this->assertCount(1, $mapped);
        $row = $mapped[0];

        // Rate: 950.00
        $this->assertContains('950.00', $row);
        // Discount %: 5.00
        $this->assertContains('5.00', $row);
        // Discount Amount: 47.50
        $this->assertContains('47.50', $row);
        // Net Rate: 902.50
        $this->assertContains('902.50', $row);
        // Item Amount: 9,025.00
        $this->assertContains('9,025.00', $row);
    }
}
