<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/login'); 

// login
Route::get('/login', [Controllers\LoginController::class, 'create'])->name('login');
Route::post('/login', [Controllers\LoginController::class, 'store']);

// logout
Route::match(['get', 'post'], '/logout', Controllers\LogoutController::class)->name('logout')->middleware('auth');


// settings
Route::middleware('auth')->group(function () {

    // dashboard 
    Route::get('/dashboard', Controllers\DashboardController::class)->name('dashboard');
        
    // company settings   
    Route::resource('companies', Controllers\CompanyController::class)->only(['edit', 'update']);

    // users
    Route::resource('users', Controllers\UserController::class);

    // roles
    Route::resource('roles', Controllers\RoleController::class);

    // permissions
    Route::resource('permissions', Controllers\PermissionController::class);

    // storage link
    Route::get('storage/link', Controllers\StorageLinkController::class)->name('storage.link.create');

    // notifications
    Route::get('notifications', Controllers\NotificationController::class)->name('notifications.index');

    // notifications mark
    Route::match(['get', 'post'], 'notifications/mark', Controllers\NotificationMarkController::class)->name('notifications.mark');

    // profile    
    Route::get('users/{user}/profile', [Controllers\UserProfileController::class, 'show'])->name('users.profile.show');

    Route::put('users/{user}/profile', [Controllers\UserProfileController::class, 'update'])->name('users.profile.update');    

});


// general
Route::middleware('auth')->group(function () {

    
    // products images
    Route::post('products/images', [Controllers\ProductImageController::class, 'store'])->name('products.images.store');

    Route::delete('products/attachments/{image?}', [Controllers\ProductImageController::class, 'destroy'])->name('products.images.destroy');

    // products
    Route::resource('products', Controllers\ProductController::class);
    
    // units
    Route::resource('units', Controllers\UnitController::class);

    // groups
    Route::resource('groups', Controllers\GroupController::class);

    // categories
    Route::resource('categories', Controllers\CategoryController::class);

    // purposes
    Route::resource('purposes', Controllers\PurposeController::class);
    
    // transports
    Route::resource('transports', Controllers\TransportController::class);

    // designations
    Route::resource('designations', Controllers\DesignationController::class);

    // segments
    Route::resource('segments', Controllers\SegmentController::class);

    // customer types
    Route::resource('customer-types', Controllers\CustomerTypeController::class)->parameters(['customer-types' => 'type']);
    
    // customers
    Route::resource('customers', Controllers\CustomerController::class);

    // supplier types
    Route::resource('supplier-types', Controllers\SupplierTypeController::class)->parameters(['supplier-types' => 'type']);

    // suppliers
    Route::resource('suppliers', Controllers\SupplierController::class);

    // departments
    Route::resource('departments', Controllers\DepartmentController::class);  

    // employees
    Route::resource('employees', Controllers\EmployeeController::class);

});


// crm
Route::middleware('auth')->group(function () {
    
    // events
    Route::resource('events', Controllers\EventController::class);   
    
    // news images
    Route::post('news/images', [Controllers\NewsImageController::class, 'store'])->name('news.images.store');

    Route::delete('news/{image?}', [Controllers\NewsImageController::class, 'destroy'])->name('news.images.destroy');
    
    // news
    Route::resource('news', Controllers\NewsController::class);  


    // visits aging report
    Route::get('visits-aging', Controllers\VisitAgingController::class)->name('visits.aging');

    // visit attachments
    Route::post('visits/attachments', [Controllers\VisitAttachmentController::class, 'store'])->name('visits.attachments.store');

    Route::delete('visits/attachments/{attachment?}', [Controllers\VisitAttachmentController::class, 'destroy'])->name('visits.attachments.destroy');

    // visits
    Route::resource('visits', Controllers\VisitController::class);
    
    // quotation attachments
    Route::post('quotations/attachments', [Controllers\QuotationAttachmentController::class, 'store'])->name('quotations.attachments.store');

    Route::delete('quotations/attachments/{attachment?}', [Controllers\QuotationAttachmentController::class, 'destroy'])->name('quotations.attachments.destroy');
    
    // quotations
    Route::resource('quotations', Controllers\QuotationController::class);

    // invoice attachments
    Route::post('invoice/attachments', [Controllers\InvoiceAttachmentController::class, 'store'])->name('invoices.attachments.store');

    Route::delete('invoices/attachments/{attachment?}', [Controllers\InvoiceAttachmentController::class, 'destroy'])->name('invoices.attachments.destroy');

    // invoice
    Route::resource('invoices', Controllers\InvoiceController::class);

    // bills
    Route::resource('bills', Controllers\BillController::class);

    // bill attachments
    Route::post('bills/attachments', [Controllers\BillAttachmentController::class, 'store'])->name('bills.attachments.store');

    Route::delete('bills/attachments/{attachment?}', [Controllers\BillAttachmentController::class, 'destroy'])->name('bills.attachments.destroy');
    
    // targets
    Route::resource('targets', Controllers\TargetController::class);  

    // leave
    Route::resource('leaves', Controllers\LeaveController::class)->parameters(['leaves' => 'leave']);
    
    // tasks
    Route::resource('tasks', Controllers\TaskController::class);  

    // task comments
    Route::resource('tasks.comments', Controllers\TaskCommentController::class)->except('index', 'create');

    // widgets
    Route::get('visit-summary', Controllers\VisitSummaryController::class)->name('visits.summary');
    
    // visits chart
    Route::get('visit-chart', Controllers\VisitChartController::class)->name('visits.chart');

    // visits export
    Route::get('visit-export', Controllers\VisitExportController::class)->name('visits.export');

    
});


// tally 

Route::middleware('auth')->name('tally.')->prefix('tally')->group(function () {


    // items
    Route::view('items', 'tally.items')->name('items.index');

    // sales
    Route::view('sales', 'tally.sales.index')->name('sales.index');

    Route::get('sales/{sale}', function(App\Models\Tally\Sale $sale){

        return view('tally.sales.show', compact('sale'));
        
    })->name('sales.show');


    // purchases
    Route::view('purchases', 'tally.purchases.index')->name('purchases.index');

    Route::get('purchases/{purchase}', function(App\Models\Tally\Purchase $purchase){

        return view('tally.purchases.show', compact('purchase'));
        
    })->name('purchases.show');


    // receipts
    Route::view('receipts', 'tally.receipts.index')->name('receipts.index');

    Route::get('receipts/{receipt}', function(App\Models\Tally\Receipt $receipt){

        return view('tally.receipts.show', compact('receipt'));
        
    })->name('receipts.show');

    
    // payments
    Route::view('payments', 'tally.payments.index')->name('payments.index');

    Route::get('payments/{payment}', function(App\Models\Tally\Payment $payment){

        return view('tally.payments.show', compact('payment'));
        
    })->name('payments.show');


    Route::view('ledgers', 'tally.ledgers')->name('ledgers.index');

    Route::view('stocks', 'tally.stocks')->name('stocks.index');

});


// settings
Route::view('settings', 'settings.index')->name('settings.index')->middleware('auth');


// reports
Route::view('reports', 'reports.index')->name('reports.index')->middleware('auth');


