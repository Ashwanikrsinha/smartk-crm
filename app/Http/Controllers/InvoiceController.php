<?php

namespace App\Http\Controllers;

use App\Mail\PoApprovedMail;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Product;
use App\Models\Visit;
use App\Models\Unit;
use App\Models\User;
use App\Models\LeadSource;
use App\Models\State;
use App\Notifications\PoApproved;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Services\PoDocumentService;
use App\Jobs\SendPoMailToSchool;
use App\Jobs\SendPoMailToSp;
use App\Jobs\SendPoMailToAccounts;
use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Invoice::class, 'invoice');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $teamIds = auth()->user()->teamMemberIds();

            $invoices = Invoice::with([
                'customer:id,name,school_code',
                'user:id,username',
            ])
                ->whereIn('user_id', $teamIds)
                ->select(
                    'id',
                    'po_number',
                    'invoice_number',
                    'invoice_date',
                    'customer_id',
                    'user_id',
                    'status',
                    'amount',
                    'billing_amount',
                    'collected_amount',
                    'outstanding_amount'
                );

            return DataTables::of($invoices)
                ->editColumn('po_number', fn($i) => $i->po_number ?? "PO-{$i->invoice_number}")
                ->editColumn('invoice_date', fn($i) => $i->invoice_date->format('d M, Y'))
                ->editColumn('status', fn($i) => $this->statusBadge($i->status))
                ->editColumn('amount',            fn($i) => '₹' . number_format($i->amount, 2))
                ->editColumn('billing_amount',    fn($i) => '₹' . number_format($i->billing_amount, 2))
                ->editColumn('collected_amount',  fn($i) => '₹' . number_format($i->collected_amount, 2))
                ->editColumn('outstanding_amount', fn($i) => '₹' . number_format($i->outstanding_amount, 2))
                ->addColumn('action', fn($i) => view('invoices.buttons', ['invoice' => $i]))
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('invoices.index');
    }

    public function create()
    {
        return view('invoices.create', $this->formData());
    }
    public function store(InvoiceStoreRequest $request)
    {
        DB::transaction(function () use ($request) {

            $status = $request->action === 'submit'
                ? Invoice::STATUS_SUBMITTED
                : Invoice::STATUS_DRAFT;

            $invoice = Invoice::create([
                'invoice_number'   => Invoice::invoiceNumber(),
                'po_number'        => Invoice::generatePoNumber(),
                'invoice_date'     => $request->invoice_date,
                'delivery_due_date' => $request->delivery_due_date,
                'customer_id'      => $request->customer_id,
                'visit_id'         => $request->visit_id ?? null,
                'user_id'          => auth()->id(),
                'phone_number'     => $request->phone_number,
                'address'          => $request->address,
                'status'           => $status,
                'remarks'          => $request->remarks,
                'terms'            => $request->terms,
                'amount'           => $request->total_po_amount,
                'pending_po_amount' => $request->total_po_amount, // B=0 initially, so C=A
            ]);

            $invoice->createInvoiceItems($request);
            $invoice->createPdcs($request);

            if ($request->filled('attachments.0')) {
                $invoice->createInvoiceAttachments($request);
            }
        });

        return redirect()->route('invoices.index')
            ->with('success', 'Purchase Order created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load([
            'invoiceItems.product.category',
            'invoiceItems.unit',
            'customer.leadSource',
            'user',
            'approvedBy',
            'pdcs',
            'collections.collectedBy',
        ]);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        // Hard lock — cannot edit approved POs
        if ($invoice->isApproved()) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Approved orders cannot be edited.');
        }

        $invoice->load([
            'invoiceItems.product',
            'invoiceItems.unit',
            'pdcs',
        ]);

        return view('invoices.edit', array_merge(
            $this->formData(),
            ['invoice' => $invoice]
        ));
    }

    public function update(InvoiceUpdateRequest $request, Invoice $invoice)
    {
        if ($invoice->isApproved()) {
            return back()->with('error', 'Approved orders cannot be edited.');
        }
        // return $request->all();
        // Log::info("requeiuy ",['reuest all========='=>$request->all()]);
        DB::transaction(function () use ($request, $invoice) {

            $status = $request->action === 'submit'
                ? Invoice::STATUS_SUBMITTED
                : Invoice::STATUS_DRAFT;

            // If re-submitting a rejected PO, clear rejection reason
            $rejectionReason = $status === Invoice::STATUS_SUBMITTED ? null : $invoice->rejection_reason;

            $invoice->update([
                'invoice_date'      => $request->invoice_date,
                'delivery_due_date' => $request->delivery_due_date,
                'customer_id'       => $request->customer_id,
                'visit_id'          => $request->visit_id ?? null,
                'phone_number'      => $request->phone_number,
                'address'           => $request->address,
                'status'            => $status,
                'rejection_reason'  => $rejectionReason,
                'remarks'           => $request->remarks,
                'terms'             => $request->terms,
                'amount'            => $request->total_po_amount,
                'pending_po_amount' => $request->total_po_amount - $invoice->billing_amount,
            ]);

            $invoice->invoiceItems()->delete();
            $invoice->createInvoiceItems($request);
             Log::info("requeiuyreuest all====== at inside  txn === ",['req'=>$request->all()]);

            $invoice->pdcs()->delete();
            $invoice->createPdcs($request);

            if ($request->filled('attachments.0')) {
                $invoice->createInvoiceAttachments($request);
            }
        });

        return back()->with('success', 'Purchase Order updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->isApproved()) {
            return back()->with('error', 'Approved orders cannot be deleted.');
        }

        DB::transaction(function () use ($invoice) {
            foreach ($invoice->attachments as $attachment) {
                Storage::delete($attachment->filename);
            }
            $invoice->attachments()->delete();
            $invoice->invoiceItems()->delete();
            $invoice->pdcs()->delete();
            $invoice->delete();
        });

        return redirect()->route('invoices.index')
            ->with('success', 'Purchase Order deleted.');
    }

    // public function approve(Request $request, Invoice $invoice)
    // {
    //     $this->authorize('approve', $invoice);

    //     if (!$invoice->isSubmitted()) {
    //         return back()->with('error', 'Only submitted orders can be approved.');
    //     }

    //     $invoice->update([
    //         'status'      => Invoice::STATUS_APPROVED,
    //         'approved_by' => auth()->id(),
    //         'approved_at' => now(),
    //         'rejection_reason' => null,
    //     ]);

    //     // Notify SP + send email to school
    //     $this->sendApprovalNotification($invoice);

    //     return back()->with('success', "PO {$invoice->po_number} approved successfully.");
    // }

    // public function approve(Request $request, Invoice $invoice)
    // {
    //     $this->authorize('approve', $invoice);

    //     if (!$invoice->isSubmitted()) {
    //         return back()->with('error', 'Only submitted orders can be approved.');
    //     }

    //     $invoice->update([
    //         'status'           => Invoice::STATUS_APPROVED,
    //         'approved_by'      => auth()->id(),
    //         'approved_at'      => now(),
    //         'rejection_reason' => null,
    //     ]);

    //     $this->sendApprovalNotifications($invoice);

    //     return back()->with('success', "PO {$invoice->po_number} approved. Mail sent to school, SP, and Accounts team.");
    // }


    // private function sendApprovalNotifications(Invoice $invoice): void
    // {
    //     $invoice->loadMissing([
    //         'customer.contacts.designation',
    //         'invoiceItems.product.category',
    //         'pdcs',
    //         'user',
    //     ]);

    //     // 1. DB notification to SP
    //     $invoice->user->notify(new PoApproved($invoice));

    //     // 2. Email to School (if email on record)
    //     if ($invoice->customer->email) {
    //         Mail::to($invoice->customer->email)
    //             ->queue(new PoApprovedMail($invoice));
    //     }

    //     // 3. Email to SP
    //     if ($invoice->user->email) {
    //         Mail::to($invoice->user->email)
    //             ->queue(new PoApprovedMail($invoice));
    //     }

    //     // 4. Email to all Accounts team members
    //     User::where('role_id', function ($q) {
    //         $q->select('id')->from('roles')->where('name', 'Accounts');
    //     })
    //         ->whereNotNull('email')
    //         ->active()
    //         ->get()
    //         ->each(function ($accountsUser) use ($invoice) {
    //             Mail::to($accountsUser->email)
    //                 ->queue(new PoApprovedMail($invoice));
    //         });
    // }

    public function approve(Request $request, Invoice $invoice)
    {
        $this->authorize('approve', $invoice);

        if (!$invoice->isSubmitted()) {
            return back()->with('error', 'Only submitted orders can be approved.');
        }

        // 1. Mark approved
        $invoice->update([
            'status'           => Invoice::STATUS_APPROVED,
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'rejection_reason' => null,
        ]);

        // 2. ALWAYS generate and save the DOCX first (synchronous, not queued)
        //    So the file exists in storage before any mail job runs.
        try {
            app(PoDocumentService::class)->generate($invoice);
        } catch (\Throwable $e) {
            Log::error('PO DOCX generation failed on approval', [
                'invoice_id' => $invoice->id,
                'error'      => $e->getMessage(),
            ]);
            // Don't abort — approval is done, document can be regenerated manually
        }

        // 3. DB notification to SP
        $invoice->user->notify(new PoApproved($invoice));

        // 4. Queue mail jobs (each independently retries on failure)
        dispatch(new SendPoMailToSchool($invoice))->onQueue('mails');
        dispatch(new SendPoMailToSp($invoice))->onQueue('mails');
        dispatch(new SendPoMailToAccounts($invoice))->onQueue('mails');

        return back()->with(
            'success',
            "PO {$invoice->po_number} approved. Document saved. Mails queued for School, SP, and Accounts."
        );
    }
    public function downloadDocument(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $service = app(PoDocumentService::class);

        // Regenerate if missing (e.g. file deleted, or generation failed earlier)
        if (!$service->exists($invoice)) {
            if (!$invoice->isApproved()) {
                return back()->with('error', 'Document is only available for approved POs.');
            }

            try {
                $service->generate($invoice);
            } catch (\Throwable $e) {
                return back()->with('error', 'Could not generate PO document: ' . $e->getMessage());
            }
        }

        $fullPath = $service->storagePath($invoice);
        $filename = "Purchase_Order_{$invoice->po_number}.docx";

        return response()->download(
            $fullPath,
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
        );
    }


    public function resendMail(Request $request, Invoice $invoice)
    {
        $this->authorize('approve', $invoice); // SM / Admin only

        if (!$invoice->isApproved()) {
            return back()->with('error', 'Mails can only be resent for approved POs.');
        }

        $service = app(PoDocumentService::class);

        // Ensure document exists
        if (!$service->exists($invoice)) {
            try {
                $service->generate($invoice);
            } catch (\Throwable $e) {
                return back()->with('error', 'Could not generate PO document: ' . $e->getMessage());
            }
        }

        $to = $request->input('to', 'all'); // 'school' | 'sp' | 'accounts' | 'all'

        if (in_array($to, ['school', 'all'])) {
            // Reset sent_at so job re-stamps it on success
            $invoice->updateQuietly(['school_mail_sent_at' => null]);
            dispatch(new SendPoMailToSchool($invoice))->onQueue('mails');
        }

        if (in_array($to, ['sp', 'all'])) {
            $invoice->updateQuietly(['sp_mail_sent_at' => null]);
            dispatch(new SendPoMailToSp($invoice))->onQueue('mails');
        }

        if (in_array($to, ['accounts', 'all'])) {
            $invoice->updateQuietly(['accounts_mail_sent_at' => null]);
            dispatch(new SendPoMailToAccounts($invoice))->onQueue('mails');
        }

        $label = $to === 'all' ? 'School, SP, and Accounts' : ucfirst($to);

        return back()->with('success', "Mail re-queued for: {$label}.");
    }

    public function regenerateDocument(Invoice $invoice)
    {
        $this->authorize('approve', $invoice); // SM / Admin only

        if (!$invoice->isApproved()) {
            return back()->with('error', 'Document can only be regenerated for approved POs.');
        }

        try {
            app(PoDocumentService::class)->regenerate($invoice);
            return back()->with('success', 'PO document regenerated successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Regeneration failed: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Invoice $invoice)
    {
        $this->authorize('approve', $invoice);

        $request->validate([
            'rejection_reason' => 'required|string|min:20',
        ], [
            'rejection_reason.min' => 'Rejection reason must be at least 20 characters.',
        ]);

        if (!$invoice->isSubmitted()) {
            return back()->with('error', 'Only submitted orders can be rejected.');
        }

        $invoice->update([
            'status'           => Invoice::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', "PO {$invoice->po_number} rejected and returned to SP.");
    }

    public function getSchool(Customer $customer): JsonResponse
    {
        $customer->load('leadSource', 'contacts');

        return response()->json([
            'id'           => $customer->id,
            'name'         => $customer->name,
            'school_code'  => $customer->school_code,
            'phone_number' => $customer->phone_number,
            'address'      => $customer->address,
            'state'        => $customer->state,
            'city'         => $customer->city,
            'pin_code'     => $customer->pin_code,
            'gstin'        => $customer->gstin,
            'lead_source_id' => $customer->lead_source_id,
            'email'        => $customer->email,
        ]);
    }

    public function getProductsByCategory(Request $request, $category): JsonResponse
    {
        Log::info('getProductsByCategory', $request->all());
        $products = Product::where('category_id', $category)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'code']);
        // ->get(['id', 'name', 'price', 'mrp', 'school_price', 'code']);

        return response()->json($products);
    }

    public function getVisitsBySchool(Request $request): JsonResponse
    {
        $visits = Visit::where('customer_id', $request->customer_id)
            ->where('user_id', auth()->id())
            ->orderByDesc('visit_date')
            ->get(['id', 'visit_number', 'visit_date', 'description']);

        return response()->json($visits);
    }

    private function formData(): array
    {
        return [
            'categories'   => Category::orderBy('name')->get(['id', 'name']),
            'units'        => Unit::orderBy('name')->pluck('name', 'id'),
            'customers'    => Customer::orderBy('name')->get(['id', 'name', 'school_code', 'city', 'state']),
            'lead_sources' => LeadSource::orderBy('name')->pluck('name', 'id'),
            'states'       => State::orderBy('name')->pluck('name', 'name'),
            'visits'       => collect(), // populated via AJAX when school selected
        ];
    }

    private function statusBadge(string $status): string
    {
        $map = [
            Invoice::STATUS_DRAFT     => 'secondary',
            Invoice::STATUS_SUBMITTED => 'warning',
            Invoice::STATUS_APPROVED  => 'success',
            Invoice::STATUS_REJECTED  => 'danger',
        ];

        $color = $map[$status] ?? 'secondary';
        return "<span class=\"badge bg-{$color}\">" . ucfirst($status) . "</span>";
    }

    private function sendApprovalNotification(Invoice $invoice): void
    {
        try {
            // DB notification to SP
            $invoice->user->notify(new \App\Notifications\PoApproved($invoice));

            // Email to school (if email exists)
            if ($invoice->customer->email) {
                Mail::to($invoice->customer->email)
                    ->queue(new \App\Mail\PoApprovedMail($invoice));
            }
        } catch (\Exception $e) {
            Log::error('Error sending approval notification:', $e->getMessage());
            throw $e;
        }
    }
    public function itemRowTemplate(Request $request): string
    {
        $idx        = (int) $request->input('idx', 0);
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('invoices.partials.item-row', [
            'idx'        => $idx,
            'item'       => null,
            'categories' => $categories,
        ])->render();
    }
}
