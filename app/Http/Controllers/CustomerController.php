<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Designation;
use App\Models\LeadSource;
use App\Models\Segment;
use App\Models\State;
use App\Models\SchoolDocument;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Customer::class, 'customer');
    }

    // -------------------------------------------------------
    // INDEX
    // -------------------------------------------------------

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $teamIds = auth()->user()->teamMemberIds();

            $schools = Customer::with([
                'createdBy:id,username',
                'leadSource:id,name',
            ])
                ->whereIn('created_by', $teamIds)
                ->select(
                    'id',
                    'school_code',
                    'name',
                    'city',
                    'state',
                    'phone_number',
                    'email',
                    'lead_source_id',
                    'created_by'
                );

            return DataTables::of($schools)
                ->editColumn('school_code', fn($s) => "<strong>{$s->school_code}</strong>")
                ->addColumn('lead_source',  fn($s) => $s->leadSource?->name ?? '—')
                ->addColumn('created_by',   fn($s) => $s->createdBy?->username ?? '—')
                ->addColumn('po_count',     fn($s) => $s->invoices()->count())
                ->addColumn('action',       fn($s) => view('customers.buttons', ['customer' => $s]))
                ->rawColumns(['school_code', 'action'])
                ->make(true);
        }

        return view('customers.index');
    }
    public function create()
    {
        return view('customers.create', $this->formData());
    }
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'phone_number'    => 'required|string|max:15',
            'state'           => 'required|string|max:100',
            'city'            => 'required|string|max:100',
            'email'           => 'nullable|email|max:255',
            'address'         => 'nullable|string|max:500',
            'pin_code'        => 'nullable|string|max:10',
            'gstin'           => [
                'nullable',
                'string',
                'size:15',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'
            ],
            'lead_source_id'  => 'nullable|exists:lead_sources,id',
            // 'segment_id'      => 'nullable|exists:segments,id',
            'description'     => 'nullable|string|max:1000',
            // Contacts
            'person_name'     => 'nullable|array',
            'person_name.*'   => 'nullable|string|max:255',
            'contact_number'  => 'nullable|array',
            'designation_id'  => 'nullable|array',
            // Documents
            'aadhar'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pan'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'gst_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $customer = DB::transaction(function () use ($request) {

            $customer = Customer::create([
                'school_code'    => Customer::generateSchoolCode(),
                'name'           => $request->name,
                'phone_number'   => $request->phone_number,
                'email'          => $request->email,
                'address'        => $request->address,
                'state'          => $request->state,
                'city'           => $request->city,
                'pin_code'       => $request->pin_code,
                'gstin'          => $request->gstin,
                'lead_source_id' => $request->lead_source_id,
                'segment_id'     => $request->segment_id,
                'description'    => $request->description,
                'created_by'     => auth()->id(),
            ]);

            // Save contacts
            if ($request->filled('person_name.0')) {
                $customer->createContacts($request);
            }

            // Upload documents
            foreach (SchoolDocument::types() as $type) {
                if ($request->hasFile($type)) {
                    $ext  = $request->file($type)->getClientOriginalExtension();
                    $path = $request->file($type)->storeAs(
                        "school-docs/{$customer->school_code}",
                        "{$type}.{$ext}",
                        'public'
                    );
                    SchoolDocument::create([
                        'customer_id' => $customer->id,
                        'type'        => $type,
                        'filename'    => $path,
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            return $customer;
        });

        // AJAX (from new-school-modal in PO form)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id'             => $customer->id,
                'name'           => $customer->name,
                'school_code'    => $customer->school_code,
                'city'           => $customer->city,
                'state'          => $customer->state,
                'phone_number'   => $customer->phone_number,
                'address'        => $customer->address,
                'email'          => $customer->email,
                'lead_source_id' => $customer->lead_source_id,
            ], 201);
        }

        return redirect()->route('customers.show', $customer)
            ->with('success', "School {$customer->school_code} registered successfully.");
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'leadSource',
            'createdBy:id,username',
            'contacts.designation',
            'documents',
            'invoices' => function ($q) {
                $q->select(
                    'id',
                    'po_number',
                    'invoice_date',
                    'customer_id',
                    'user_id',
                    'status',
                    'amount',
                    'billing_amount',
                    'collected_amount',
                    'outstanding_amount'
                )
                    ->with('user:id,username')
                    ->orderByDesc('invoice_date');
            },
        ]);

        $financials = [
            'total_po'          => $customer->invoices->sum('amount'),
            'total_billed'      => $customer->invoices->sum('billing_amount'),
            'total_collected'   => $customer->invoices->sum('collected_amount'),
            'total_outstanding' => $customer->invoices->sum('outstanding_amount'),
        ];

        $documentTypes = SchoolDocument::types();

        return view('customers.show', compact('customer', 'financials', 'documentTypes'));
    }
    public function edit(Customer $customer)
    {
        $customer->load('contacts', 'documents');

        return view('customers.edit', array_merge(
            $this->formData(),
            ['customer' => $customer]
        ));
    }
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'phone_number'   => 'required|string|max:15',
            'state'          => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
            'pin_code'       => 'nullable|string|max:10',
            'gstin'          => [
                'nullable',
                'string',
                'size:15',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'
            ],
            'lead_source_id' => 'nullable|exists:lead_sources,id',
            // 'segment_id'     => 'nullable|exists:segments,id',
            'description'    => 'nullable|string|max:1000',
            'person_name'    => 'nullable|array',
            'person_name.*'  => 'nullable|string|max:255',
            'contact_number' => 'nullable|array',
            'designation_id' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $customer) {

            $customer->update([
                'name'           => $request->name,
                'phone_number'   => $request->phone_number,
                'email'          => $request->email,
                'address'        => $request->address,
                'state'          => $request->state,
                'city'           => $request->city,
                'pin_code'       => $request->pin_code,
                'gstin'          => $request->gstin,
                'lead_source_id' => $request->lead_source_id,
                'segment_id'     => $request->segment_id,
                'description'    => $request->description,
            ]);

            // Contacts: delete all and recreate (same pattern as existing codebase)
            if ($request->filled('person_name.0')) {
                $customer->contacts()->delete();
                $customer->createContacts($request);
            }
        });

        return back()->with('success', 'School details updated.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->invoices()->count() > 0) {
            return back()->with('error', 'Cannot delete a school with existing Purchase Orders.');
        }

        foreach ($customer->documents as $doc) {
            Storage::disk('public')->delete($doc->filename);
        }

        DB::transaction(function () use ($customer) {
            $customer->documents()->delete();
            $customer->contacts()->delete();
            $customer->delete();
        });

        return redirect()->route('customers.index')->with('success', 'School deleted.');
    }
    private function formData(): array
    {
        return [
            'lead_sources' => LeadSource::orderBy('name')->pluck('name', 'id'),
            // 'segments'     => Segment::orderBy('name')->pluck('name', 'id'),
            'states'       => State::orderBy('name')->pluck('name', 'name'),
            'designations' => Designation::orderBy('name')->pluck('name', 'id'), // needed for contacts table
        ];
    }
}
