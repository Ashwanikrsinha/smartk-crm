<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // -------------------------------------------------------
    // INDEX
    // -------------------------------------------------------

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $user  = auth()->user();
            $query = Leave::select('leaves.*')->with('user:id,username,emp_code');

            // Scope: SP sees own, SM sees their team's, Admin sees all
            if ($user->isSalesPerson()) {
                $query->where('user_id', $user->id);
            } elseif ($user->isSalesManager()) {
                $teamIds = $user->teamMemberIds();
                $query->whereIn('user_id', $teamIds);
            }
            // Admin: no scope, sees all

            return DataTables::of($query)
                ->editColumn('from_date', fn($l) => $l->from_date->format('d M, Y'))
                ->editColumn('to_date',   fn($l) => $l->to_date->format('d M, Y'))
                ->addColumn('days', fn($l) => $l->from_date->diffInDays($l->to_date) + 1)
                ->editColumn('status', fn($l) => $this->statusBadge($l->status))
                ->addColumn('action', fn($l) => view('leaves.buttons', ['leave' => $l]))
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        // Leave balance for SP
        $leaveBalance = null;
        if (auth()->user()->isSalesPerson()) {
            $leaveBalance = $this->getLeaveBalance(auth()->user());
        }

        return view('leaves.index', compact('leaveBalance'));
    }

    // -------------------------------------------------------
    // CREATE
    // -------------------------------------------------------

    public function create()
    {
        $leaveBalance = $this->getLeaveBalance(auth()->user());
        $leaveTypes   = Leave::types();

        return view('leaves.create', compact('leaveBalance', 'leaveTypes'));
    }

    // -------------------------------------------------------
    // STORE — leave goes to SP's assigned SM only
    // -------------------------------------------------------

    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|in:' . implode(',', Leave::types()),
            'from_date' => 'required|date|after_or_equal:today',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'comment'     => 'required|string|min:10|max:500',
        ]);

        $user = auth()->user();

        // SP must have a manager assigned
        if ($user->isSalesPerson() && !$user->reportive_id) {
            return back()->withErrors(['error' => 'No Sales Manager assigned to your account. Contact Admin.']);
        }

        $leave = Leave::create([
            'user_id'    => $user->id,
            'leave_number' => Leave::leaveNumber(),
            'leave_type' => $request->leave_type,
            'from_date' => $request->from_date,
            'to_date'   => $request->to_date,
            'comment'     => $request->comment,
            'status'     => Leave::STATUS_PENDING,
        ]);

        // Notify ONLY the SP's assigned SM (not all managers)
        if ($user->reportive_id) {
            $manager = User::find($user->reportive_id);
            if ($manager) {
                $manager->notify(new \App\Notifications\LeaveRequested($leave));
            }
        }

        return redirect()->route('leaves.index')
            ->with('success', 'Leave application submitted to your Sales Manager.');
    }
    public function show(Leave $leave)
    {
        $this->authorizeLeaveAccess($leave);
        return view('leaves.show', compact('leave'));
    }
    public function edit(Leave $leave)
    {
        $this->authorizeLeaveAccess($leave);

        if (!$leave->isPending()) {
            return back()->with('error', 'Only pending leaves can be edited.');
        }

        $leaveTypes = Leave::types();
        return view('leaves.edit', compact('leave', 'leaveTypes'));
    }
    public function update(Request $request, Leave $leave)
    {
        $this->authorizeLeaveAccess($leave);

        $user = auth()->user();

        // SM approving or rejecting
        if ($user->isSalesManager() || $user->isAdmin()) {

            $request->validate([
                'status'          => 'required|in:approved,rejected',
                'manager_remarks' => 'nullable|string|max:300',
            ]);

            $leave->update([
                'status'          => $request->status,
                'manager_remarks' => $request->manager_remarks,
            ]);

            // Notify SP about decision
            $leave->user->notify(new \App\Notifications\LeaveDecided($leave));

            return back()->with('success', 'Leave ' . $request->status . '.');
        }

        // SP editing their own pending leave
        if (!$leave->isPending()) {
            return back()->with('error', 'Only pending leaves can be edited.');
        }

        $request->validate([
            'leave_type' => 'required|in:' . implode(',', Leave::types()),
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'comment'     => 'required|string|min:10|max:500',
        ]);

        $leave->update($request->only(['leave_type', 'from_date', 'to_date', 'comment']));

        return back()->with('success', 'Leave updated.');
    }

    // -------------------------------------------------------
    // DESTROY
    // -------------------------------------------------------

    public function destroy(Leave $leave)
    {
        $this->authorizeLeaveAccess($leave);

        if (!$leave->isPending()) {
            return back()->with('error', 'Only pending leaves can be cancelled.');
        }

        $leave->delete();
        return redirect()->route('leaves.index')->with('success', 'Leave cancelled.');
    }

    // -------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------

    /**
     * Calculate leave balance for a user.
     * Annual allowance: 12 casual + 12 sick = 24 total per year.
     */
    private function getLeaveBalance(User $user): array
    {
        $year = date('Y');

        $taken = Leave::where('user_id', $user->id)
            ->where('status', Leave::STATUS_APPROVED)
            ->whereYear('from_date', $year)
            ->selectRaw("leave_type, SUM(DATEDIFF(to_date, from_date) + 1) as days_taken")
            ->groupBy('leave_type')
            ->get()
            ->keyBy('leave_type');

        $allowances = [
            'casual'    => 12,
            'sick'      => 12,
            'earned'    => 15,
            'maternity' => 90,
        ];

        $balance = [];
        foreach ($allowances as $type => $allowed) {
            $used = (int) ($taken[$type]->days_taken ?? 0);
            $balance[$type] = [
                'allowed'   => $allowed,
                'used'      => $used,
                'remaining' => max($allowed - $used, 0),
            ];
        }

        return $balance;
    }

    /**
     * Authorize access to a leave record based on role.
     */
    private function authorizeLeaveAccess(Leave $leave): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) return;

        // SM can access their team's leaves
        if ($user->isSalesManager()) {
            $teamIds = $user->teamMemberIds();
            if (!in_array($leave->user_id, $teamIds)) {
                abort(403);
            }
            return;
        }

        // SP can only access own leaves
        if ($leave->user_id != $user->id) {
            abort(403);
        }
    }

    private function statusBadge(string $status): string
    {
        $map = [
            'pending'  => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ];
        $color = $map[$status] ?? 'secondary';
        return "<span class=\"badge bg-{$color}\">" . ucfirst($status) . "</span>";
    }
}
