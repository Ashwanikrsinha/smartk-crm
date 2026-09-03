<?php

namespace Tests\Feature;

use App\Exports\PurchaseOrdersExport;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductAndInvoiceMrpTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $category;
    protected $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $perms = ['create_products', 'edit_products', 'browse_products', 'create_invoices'];
        foreach ($perms as $p) {
            $perm = Permission::firstOrCreate(['name' => $p]);
            $adminRole->permissions()->syncWithoutDetaching([$perm->id]);
        }

        $this->admin = User::create([
            'username' => 'test_admin_' . uniqid(),
            'emp_code' => 'EMP-ADM-' . uniqid(),
            'email'    => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $adminRole->id,
        ]);

        $this->category = Category::firstOrCreate(['name' => 'Robotics']);
        $this->unit = Unit::firstOrCreate(['name' => 'PCS']);
    }

    public function test_can_create_product_without_price_hsn_reorder_level()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('products.store'), [
            'name'        => 'Kit Test Product',
            'category_id' => $this->category->id,
            'unit_id'     => $this->unit->id,
            // price, code, reorder_level omitted
        ]);

        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'name'          => 'Kit Test Product',
            'price'         => 0,
            'code'          => '',
            'reorder_level' => 0,
        ]);
    }

    public function test_can_update_product_with_zero_price()
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'name'          => 'Original Product',
            'category_id'   => $this->category->id,
            'unit_id'       => $this->unit->id,
            'price'         => 500,
            'code'          => '123456',
            'reorder_level' => 10,
        ]);

        $response = $this->put(route('products.update', $product), [
            'name'          => 'Updated Product',
            'category_id'   => $this->category->id,
            'unit_id'       => $this->unit->id,
            'price'         => '',
            'code'          => '',
            'reorder_level' => '',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id'            => $product->id,
            'name'          => 'Updated Product',
            'price'         => 0,
            'code'          => '',
            'reorder_level' => 0,
        ]);
    }

    public function test_invoice_export_reconstructs_mrp_when_product_price_is_zero()
    {
        $customer = Customer::create([
            'school_code'  => 'SCH-MRP-' . rand(1000, 9999),
            'name'         => 'MRP School',
            'phone_number' => '9876543210',
            'state'        => 'Maharashtra',
            'city'         => 'Nagpur',
            'created_by'   => $this->admin->id,
        ]);

        $product = Product::create([
            'name'        => 'Zero Price Product',
            'price'       => 0,
            'unit_id'     => $this->unit->id,
            'category_id' => $this->category->id,
        ]);

        $invoice = Invoice::create([
            'po_number'       => 'PO-MRP-' . rand(1000, 9999),
            'invoice_date'    => now(),
            'user_id'         => $this->admin->id,
            'customer_id'     => $customer->id,
            'status'          => 'approved',
            'amount'          => 9025.00,
            'billing_amount'  => 0,
            'collected_amount'=> 0,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'quantity'   => 10,
            'unit_id'    => $this->unit->id,
            'rate'       => 902.50, // 950 - 5% discount
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

        $export = new PurchaseOrdersExport([], $this->admin);
        $mapped = $export->map($invoice);
        $row = $mapped[0];

        // Rate: reconstructed as 950.00
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
