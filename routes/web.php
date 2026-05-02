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

// ── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login',  [Controllers\LoginController::class, 'create'])->name('login');
Route::post('/login', [Controllers\LoginController::class, 'store']);
Route::match(['get','post'], '/logout', Controllers\LogoutController::class)
     ->name('logout')->middleware('auth');

// ═════════════════════════════════════════════════════════════════════════════
// ALL AUTHENTICATED ROUTES
// ═════════════════════════════════════════════════════════════════════════════
Route::middleware('auth')->group(function () {

    // ── Dashboard ────────────────────────────────────────────────────────────
    Route::get('/dashboard', Controllers\DashboardController::class)->name('dashboard');

    // ── Settings & Company ───────────────────────────────────────────────────
    Route::resource('companies', Controllers\CompanyController::class)->only(['edit','update']);
    Route::view('settings', 'settings.index')->name('settings.index');

    // ── Users & Auth ─────────────────────────────────────────────────────────
    Route::resource('users', Controllers\UserController::class);
    Route::get('users/{user}/profile',  [Controllers\UserProfileController::class, 'show'])->name('users.profile.show');
    Route::put('users/{user}/profile',  [Controllers\UserProfileController::class, 'update'])->name('users.profile.update');
    Route::resource('roles',       Controllers\RoleController::class);
    Route::resource('permissions', Controllers\PermissionController::class);

    // ── Misc ─────────────────────────────────────────────────────────────────
    Route::get('storage/link', Controllers\StorageLinkController::class)->name('storage.link.create');
    Route::get('notifications', Controllers\NotificationController::class)->name('notifications.index');
    Route::match(['get','post'], 'notifications/mark', Controllers\NotificationMarkController::class)->name('notifications.mark');

    // ═════════════════════════════════════════════════════════════════════════
    // MASTER DATA
    // ═════════════════════════════════════════════════════════════════════════

    Route::post('products/images',             [Controllers\ProductImageController::class, 'store'])->name('products.images.store');
    Route::delete('products/attachments/{image?}', [Controllers\ProductImageController::class, 'destroy'])->name('products.images.destroy');
    Route::resource('products',     Controllers\ProductController::class);
    Route::resource('units',        Controllers\UnitController::class);
    Route::resource('groups',       Controllers\GroupController::class);
    Route::resource('categories',   Controllers\CategoryController::class);
    Route::resource('purposes',     Controllers\PurposeController::class);
    Route::resource('transports',   Controllers\TransportController::class);
    Route::resource('designations', Controllers\DesignationController::class);
    Route::resource('segments',     Controllers\SegmentController::class);
    Route::resource('departments',  Controllers\DepartmentController::class);
    Route::resource('employees',    Controllers\EmployeeController::class);
    Route::resource('customer-types',  Controllers\CustomerTypeController::class)->parameters(['customer-types' => 'type']);
    Route::resource('supplier-types',  Controllers\SupplierTypeController::class)->parameters(['supplier-types' => 'type']);
    Route::resource('suppliers',    Controllers\SupplierController::class);
    Route::resource('lead-sources', Controllers\LeadSourceController::class);   // ← ONE declaration only

    // ═════════════════════════════════════════════════════════════════════════
    // SCHOOLS (Customers)
    // ═════════════════════════════════════════════════════════════════════════

    Route::resource('customers', Controllers\CustomerController::class);

    // School Documents
    Route::post('school-documents',                [Controllers\SchoolDocumentController::class, 'store'])->name('school-documents.store');
    Route::post('school-documents/bulk',           [Controllers\SchoolDocumentController::class, 'bulkStore'])->name('school-documents.bulk-store');
    Route::delete('school-documents/{doc}',        [Controllers\SchoolDocumentController::class, 'destroy'])->name('school-documents.destroy');
    Route::get('school-documents/{doc}/download',  [Controllers\SchoolDocumentController::class, 'download'])->name('school-documents.download');

    // ═════════════════════════════════════════════════════════════════════════
    // VISITS
    // ═════════════════════════════════════════════════════════════════════════

    Route::get('visits-aging', Controllers\VisitAgingController::class)->name('visits.aging');
    Route::get('visit-summary', Controllers\VisitSummaryController::class)->name('visits.summary');
    Route::get('visit-chart',   Controllers\VisitChartController::class)->name('visits.chart');
    Route::get('visit-export',  Controllers\VisitExportController::class)->name('visits.export');

    // Attachments BEFORE resource to avoid wildcard conflict
    Route::post('visits/attachments',             [Controllers\VisitAttachmentController::class, 'store'])->name('visits.attachments.store');
    Route::delete('visits/attachments/{attachment?}', [Controllers\VisitAttachmentController::class, 'destroy'])->name('visits.attachments.destroy');
    Route::resource('visits', Controllers\VisitController::class);

    // ═════════════════════════════════════════════════════════════════════════
    // PURCHASE ORDERS (Invoices)
    // CRITICAL: all custom GET routes BEFORE Route::resource('invoices')
    // ═════════════════════════════════════════════════════════════════════════

    // Attachments
    Route::post('invoice/attachments',            [Controllers\InvoiceAttachmentController::class, 'store'])->name('invoices.attachments.store');
    Route::delete('invoices/attachments/{attachment?}', [Controllers\InvoiceAttachmentController::class, 'destroy'])->name('invoices.attachments.destroy');

    // AJAX helpers — all BEFORE resource()
    Route::get('invoices/school/{customer}',      [Controllers\InvoiceController::class, 'getSchool'])->name('invoices.school');
    Route::get('invoices/products/{category}',    [Controllers\InvoiceController::class, 'getProductsByCategory'])->name('invoices.products');
    Route::get('invoices/visits/{customer}',      [Controllers\InvoiceController::class, 'getVisitsBySchool'])->name('invoices.visits');
    Route::get('invoices/item-row-template',      [Controllers\InvoiceController::class, 'itemRowTemplate'])->name('invoices.item-row-template');

    // PO workflow actions — BEFORE resource()
    Route::patch('invoices/{invoice}/approve',    [Controllers\InvoiceController::class, 'approve'])->name('invoices.approve');
    Route::patch('invoices/{invoice}/reject',     [Controllers\InvoiceController::class, 'reject'])->name('invoices.reject');

    // PO document actions
    Route::get('invoices/{invoice}/document/download',
        [Controllers\InvoiceController::class, 'downloadDocument'])->name('invoices.document.download');
    Route::patch('invoices/{invoice}/document/regenerate',
        [Controllers\InvoiceController::class, 'regenerateDocument'])->name('invoices.document.regenerate');
    Route::post('invoices/{invoice}/mail/resend',
        [Controllers\InvoiceController::class, 'resendMail'])->name('invoices.mail.resend');

    // Resource LAST
    Route::resource('invoices', Controllers\InvoiceController::class);

    // ═════════════════════════════════════════════════════════════════════════
    // BILLS (Sales Bill)
    // ═════════════════════════════════════════════════════════════════════════

    Route::get('invoices/school-from-po/{invoice}',
        [Controllers\BillController::class, 'schoolFromPo'])->name('bills.school-from-po');

    Route::post('bills/attachments',              [Controllers\BillAttachmentController::class, 'store'])->name('bills.attachments.store');
    Route::delete('bills/attachments/{attachment?}', [Controllers\BillAttachmentController::class, 'destroy'])->name('bills.attachments.destroy');
    Route::resource('bills', Controllers\BillController::class);

    // ═════════════════════════════════════════════════════════════════════════
    // COLLECTIONS & BILLING
    // CRITICAL: POST saveSingle + bulk BEFORE resource()
    // ═════════════════════════════════════════════════════════════════════════

    Route::post('collections/save-single',        [Controllers\CollectionController::class, 'saveSingle'])->name('collections.save-single');
    Route::post('collections/bulk-store',         [Controllers\CollectionController::class, 'bulkStore'])->name('collections.bulk-store');
    Route::get('collections/invoice/{invoice}',   [Controllers\CollectionController::class, 'forInvoice'])->name('collections.for-invoice');

    // Resource — only needed actions (no store/destroy)
    Route::resource('collections', Controllers\CollectionController::class)->only(['index','edit','update']);

    // ═════════════════════════════════════════════════════════════════════════
    // PDCs
    // ═════════════════════════════════════════════════════════════════════════

    Route::resource('pdcs', Controllers\PdcController::class)->only(['index','update']);

    // ═════════════════════════════════════════════════════════════════════════
    // DISPATCHES (Warehouse)
    // ═════════════════════════════════════════════════════════════════════════

    // PO dispatch summary AJAX — BEFORE resource()
    Route::get('dispatches/po-summary/{invoice}',
        [Controllers\DispatchController::class, 'poSummary'])->name('dispatches.po-summary');

    Route::resource('dispatches', Controllers\DispatchController::class)->only(['index','create','store','show']);

    // ═════════════════════════════════════════════════════════════════════════
    // QUOTATIONS
    // ═════════════════════════════════════════════════════════════════════════

    Route::post('quotations/attachments',         [Controllers\QuotationAttachmentController::class, 'store'])->name('quotations.attachments.store');
    Route::delete('quotations/attachments/{attachment?}', [Controllers\QuotationAttachmentController::class, 'destroy'])->name('quotations.attachments.destroy');
    Route::resource('quotations', Controllers\QuotationController::class);

    // ═════════════════════════════════════════════════════════════════════════
    // HR — TARGETS / LEAVES / TASKS
    // ═════════════════════════════════════════════════════════════════════════

    Route::resource('targets', Controllers\TargetController::class);
    Route::resource('leaves',  Controllers\LeaveController::class)->parameters(['leaves' => 'leave']);
    Route::resource('tasks',   Controllers\TaskController::class);
    Route::resource('tasks.comments', Controllers\TaskCommentController::class)->except('index','create');

    // ═════════════════════════════════════════════════════════════════════════
    // CRM EXTRAS
    // ═════════════════════════════════════════════════════════════════════════

    Route::resource('events', Controllers\EventController::class);
    Route::post('news/images',     [Controllers\NewsImageController::class, 'store'])->name('news.images.store');
    Route::delete('news/{image?}', [Controllers\NewsImageController::class, 'destroy'])->name('news.images.destroy');
    Route::resource('news', Controllers\NewsController::class);

    // ═════════════════════════════════════════════════════════════════════════
    // REPORTS
    // ═════════════════════════════════════════════════════════════════════════

    // PO log export — specific invoice
    Route::get('reports/po-log/{invoiceId}',
        [Controllers\ReportsController::class, 'exportPoLog'])->name('reports.po-log-export');

    // All PO logs export (no invoiceId)
    Route::get('reports/po-log-all',
        [Controllers\ReportsController::class, 'exportPoLog'])->name('reports.po-log-all');

    // Main export
    Route::get('reports/export',
        [Controllers\ReportsController::class, 'export'])->name('reports.export');

    // Reports index — LAST to avoid swallowing other /reports/* routes
    Route::get('reports',
        [Controllers\ReportsController::class, 'index'])->name('reports.index');

});

// ═════════════════════════════════════════════════════════════════════════════
// TALLY (kept as-is, untouched)
// ═════════════════════════════════════════════════════════════════════════════
Route::middleware('auth')->name('tally.')->prefix('tally')->group(function () {

    Route::view('items',     'tally.items')->name('items.index');
    Route::view('sales',     'tally.sales.index')->name('sales.index');
    Route::view('purchases', 'tally.purchases.index')->name('purchases.index');
    Route::view('receipts',  'tally.receipts.index')->name('receipts.index');
    Route::view('payments',  'tally.payments.index')->name('payments.index');
    Route::view('ledgers',   'tally.ledgers')->name('ledgers.index');
    Route::view('stocks',    'tally.stocks')->name('stocks.index');

    Route::get('sales/{sale}',
        fn(App\Models\Tally\Sale $sale) => view('tally.sales.show', compact('sale'))
    )->name('sales.show');

    Route::get('purchases/{purchase}',
        fn(App\Models\Tally\Purchase $purchase) => view('tally.purchases.show', compact('purchase'))
    )->name('purchases.show');

    Route::get('receipts/{receipt}',
        fn(App\Models\Tally\Receipt $receipt) => view('tally.receipts.show', compact('receipt'))
    )->name('receipts.show');

    Route::get('payments/{payment}',
        fn(App\Models\Tally\Payment $payment) => view('tally.payments.show', compact('payment'))
    )->name('payments.show');
});
