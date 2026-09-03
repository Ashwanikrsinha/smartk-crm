<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use App\Services\PoDocumentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PoDocumentServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_po_docx_generation()
    {
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $admin = User::create([
            'username' => 'docx_adm_' . uniqid(),
            'emp_code' => 'EMP-DOCX-' . uniqid(),
            'email'    => 'docx_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $adminRole->id,
        ]);

        $customer = Customer::create([
            'school_code'  => 'SCH-DOCX-' . rand(1000, 9999),
            'name'         => 'St. Xavier School',
            'phone_number' => '9876543210',
            'state'        => 'Delhi',
            'city'         => 'New Delhi',
            'created_by'   => $admin->id,
        ]);

        $category = Category::firstOrCreate(['name' => 'Curriculum']);
        $unit = Unit::firstOrCreate(['name' => 'Kits']);

        $product = Product::create([
            'name'        => 'Level 1 Teacher Kit',
            'price'       => 5000.00,
            'unit_id'     => $unit->id,
            'category_id' => $category->id,
        ]);

        $invoice = Invoice::create([
            'po_number'       => 'PO-DOCX-' . rand(1000, 9999),
            'invoice_date'    => now(),
            'user_id'         => $admin->id,
            'customer_id'     => $customer->id,
            'status'          => 'approved',
            'amount'          => 50000.00,
            'billing_amount'  => 0,
            'collected_amount'=> 0,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'quantity'   => 10,
            'unit_id'    => $unit->id,
            'rate'       => 5000.00,
            'discount'   => 0,
            'amount'     => 50000.00,
        ]);

        $service = app(PoDocumentService::class);
        $path = $service->generate($invoice);

        $this->assertNotEmpty($path);
        $this->assertTrue($service->exists($invoice));
        $this->assertFileExists($service->storagePath($invoice));
    }
}
