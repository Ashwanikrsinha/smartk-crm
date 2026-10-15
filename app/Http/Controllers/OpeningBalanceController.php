<?php

namespace App\Http\Controllers;

use App\Exports\OpeningBalanceSampleExport;
use App\Imports\OpeningBalancesImport;
use App\Models\Customer;
use App\Models\OpeningBalanceCollection;
use App\Models\SchoolOpeningBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class OpeningBalanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─── Guard helper ─────────────────────────────────────────────────────────

    private function authorizeManage(): void
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isAccounts()) {
            abort(403, 'Only Administrators and Accounts team can manage opening balances.');
        }
    }

    // ── INDEX ─────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->authorizeManage();

        $sessionYear = (int) $request->input('session_year', SchoolOpeningBalance::currentSessionYear());
        $schoolId    = $request->input('school_id');
        $spId        = $request->input('sp_id');

        $query = SchoolOpeningBalance::with([
            'customer:id,name,school_code',
            'salesPerson:id,username,emp_code',
            'enteredBy:id,username',
        ])
        ->where('session_year', $sessionYear)
        ->when($schoolId, fn($q) => $q->where('customer_id', $schoolId))
        ->when($spId,     fn($q) => $q->where('user_id', $spId))
        ->orderByDesc('created_at');

        $records = $query->get();

        $totals = [
            'opening_amount'      => $records->sum('opening_amount'),
            'opening_collected'   => $records->sum('opening_collected'),
            'opening_outstanding' => $records->sum('opening_outstanding'),
        ];

        // Build available sessions list (from data or fallback)
        $sessionYears = SchoolOpeningBalance::select('session_year')
            ->distinct()
            ->orderByDesc('session_year')
            ->pluck('session_year')
            ->toArray();

        if (!in_array($sessionYear, $sessionYears)) {
            array_unshift($sessionYears, $sessionYear);
        }

        $schools  = Customer::orderBy('name')->get(['id', 'name', 'school_code']);
        $spUsers  = User::SalesPersonOrManager()->active()->orderBy('username')->get(['id', 'username', 'emp_code']);

        return view('opening-balances.index', compact(
            'records', 'totals', 'sessionYear', 'sessionYears',
            'schools', 'spUsers', 'schoolId', 'spId'
        ));
    }

    // ── CREATE ────────────────────────────────────────────────────────────────

    public function create()
    {
        $this->authorizeManage();

        $schools  = Customer::orderBy('name')->get(['id', 'name', 'school_code']);
        $spUsers  = User::SalesPersonOrManager()->active()->orderBy('username')->get(['id', 'username', 'emp_code']);
        $currentSessionYear = SchoolOpeningBalance::currentSessionYear();

        return view('opening-balances.create', compact('schools', 'spUsers', 'currentSessionYear'));
    }

    // ── STORE ─────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $this->authorizeManage();

        $data = $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'session_year'      => 'required|integer|min:2000|max:2100',
            'user_id'           => 'nullable|exists:users,id',
            'opening_amount'    => 'required|numeric|not_in:0',
            'opening_collected' => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string|max:1000',
        ]);

        // Prevent duplicate
        $exists = SchoolOpeningBalance::where('customer_id', $data['customer_id'])
            ->where('session_year', $data['session_year'])
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['customer_id' => 'An opening balance already exists for this school and session.']);
        }

        $openingCollected   = (float) ($data['opening_collected'] ?? 0);
        $openingAmount      = (float) $data['opening_amount'];
        $openingOutstanding = $openingAmount >= 0
            ? max(0, $openingAmount - $openingCollected)
            : ($openingAmount - $openingCollected);

        SchoolOpeningBalance::create([
            'customer_id'         => $data['customer_id'],
            'session_year'        => $data['session_year'],
            'user_id'             => $data['user_id'] ?? null,
            'opening_amount'      => $openingAmount,
            'opening_billed'      => $openingAmount,
            'opening_collected'   => $openingCollected,
            'opening_outstanding' => $openingOutstanding,
            'source'              => 'manual',
            'notes'               => $data['notes'] ?? null,
            'entered_by'          => auth()->id(),
        ]);

        return redirect()->route('opening-balances.index')
            ->with('success', 'Opening balance created successfully.');
    }

    // ── EDIT ──────────────────────────────────────────────────────────────────

    public function edit(SchoolOpeningBalance $openingBalance)
    {
        $this->authorizeManage();

        $schools  = Customer::orderBy('name')->get(['id', 'name', 'school_code']);
        $spUsers  = User::SalesPersonOrManager()->active()->orderBy('username')->get(['id', 'username', 'emp_code']);

        return view('opening-balances.edit', compact('openingBalance', 'schools', 'spUsers'));
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────

    public function update(Request $request, SchoolOpeningBalance $openingBalance)
    {
        $this->authorizeManage();

        $data = $request->validate([
            'user_id'        => 'nullable|exists:users,id',
            'opening_amount' => 'required|numeric|not_in:0',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $openingAmount    = (float) $data['opening_amount'];
        $totalCollected   = $openingBalance->collections()->sum('collected_amount');
        $outstanding      = $openingAmount >= 0
            ? max(0, $openingAmount - $totalCollected)
            : ($openingAmount - $totalCollected);

        $openingBalance->update([
            'user_id'             => $data['user_id'] ?? null,
            'opening_amount'      => $openingAmount,
            'opening_billed'      => $openingAmount,
            'opening_collected'   => $totalCollected,
            'opening_outstanding' => $outstanding,
            'notes'               => $data['notes'] ?? null,
        ]);

        return redirect()->route('opening-balances.index', ['session_year' => $openingBalance->session_year])
            ->with('success', 'Opening balance updated successfully.');
    }

    // ── DESTROY ───────────────────────────────────────────────────────────────

    public function destroy(SchoolOpeningBalance $openingBalance)
    {
        $this->authorizeManage();

        $openingBalance->delete();   // cascades to opening_balance_collections

        return redirect()->route('opening-balances.index')
            ->with('success', 'Opening balance record deleted.');
    }

    // ── SAMPLE DOWNLOAD ───────────────────────────────────────────────────────

    public function downloadSample()
    {
        $this->authorizeManage();

        return Excel::download(
            new OpeningBalanceSampleExport(),
            'SmartK_Opening_Balance_Template.xlsx'
        );
    }

    // ── BULK UPLOAD ───────────────────────────────────────────────────────────

    public function bulkUpload(Request $request)
    {
        $this->authorizeManage();

        $request->validate([
            'file'         => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'session_year' => 'required|integer|min:2000|max:2100',
        ]);

        $sessionYear = (int) $request->input('session_year');

        $import = new OpeningBalancesImport($sessionYear);
        Excel::import($import, $request->file('file'));

        $imported = $import->getImportedCount();
        $skipped  = $import->getSkippedCount();
        $errors   = $import->getErrors();

        $message = "Import complete: {$imported} record(s) imported, {$skipped} skipped.";

        if (!empty($errors)) {
            return redirect()->route('opening-balances.index', ['session_year' => $sessionYear])
                ->with('import_errors', $errors)
                ->with('import_partial', $message);
        }

        return redirect()->route('opening-balances.index', ['session_year' => $sessionYear])
            ->with('success', $message);
    }

    // ── STORE COLLECTION (Record a Payment) ───────────────────────────────────

    public function storeCollection(Request $request, SchoolOpeningBalance $openingBalance)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isAccounts()) {
            abort(403);
        }

        $data = $request->validate([
            'collected_amount'  => 'required|numeric|min:0.01',
            'payment_mode'      => 'required|in:cheque,neft,rtgs,upi,cash',
            'reference_number'  => 'nullable|string|max:100',
            'remarks'           => 'nullable|string|max:500',
            'collected_at'      => 'required|date',
        ]);

        OpeningBalanceCollection::create([
            'opening_balance_id' => $openingBalance->id,
            'collected_amount'   => $data['collected_amount'],
            'payment_mode'       => $data['payment_mode'],
            'reference_number'   => $data['reference_number'] ?? null,
            'remarks'            => $data['remarks'] ?? null,
            'collected_by'       => auth()->id(),
            'collected_at'       => $data['collected_at'],
        ]);
        // Observer auto-recalculates parent opening_outstanding

        return back()->with('success', '₹' . number_format($data['collected_amount'], 2) . ' collection recorded successfully.');
    }

    // ── DELETE COLLECTION ─────────────────────────────────────────────────────

    public function destroyCollection(OpeningBalanceCollection $collection)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isAccounts()) {
            abort(403);
        }

        $collection->delete();   // Observer auto-recalculates parent

        return back()->with('success', 'Collection record deleted.');
    }
}
