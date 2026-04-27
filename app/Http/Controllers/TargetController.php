<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Visit;
use Illuminate\Http\Request;
use App\Http\Requests\TargetRequest;
use Yajra\DataTables\DataTables;

class TargetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $user    = auth()->user();
            $teamIds = $user->teamMemberIds();

            $targets = Target::with('user:id,username')
                ->whereIn('user_id', $teamIds)
                ->select('id', 'user_id', 'target_amount', 'month', 'year');

            return DataTables::of($targets)
                ->editColumn('target_amount', fn($t) => '₹' . number_format($t->target_amount, 2))
                ->editColumn('month', fn($t) => date('F', mktime(0, 0, 0, $t->month, 1)) . ' ' . $t->year)
                ->addColumn('achieved', function ($t) {
                    // Sum approved PO amount for this SP in this month/year
                    $achieved = Invoice::where('user_id', $t->user_id)
                        ->where('status', Invoice::STATUS_APPROVED)
                        ->whereMonth('invoice_date', $t->month)
                        ->whereYear('invoice_date',  $t->year)
                        ->sum('amount');
                    return '₹' . number_format($achieved, 2);
                })
                ->addColumn('achievement_pct', function ($t) {
                    $achieved = Invoice::where('user_id', $t->user_id)
                        ->where('status', Invoice::STATUS_APPROVED)
                        ->whereMonth('invoice_date', $t->month)
                        ->whereYear('invoice_date',  $t->year)
                        ->sum('amount');
                    $pct = $t->target_amount > 0
                        ? round(($achieved / $t->target_amount) * 100)
                        : 0;
                    $color = $pct >= 100 ? 'success' : ($pct >= 60 ? 'warning' : 'danger');
                    return "<div class=\"progress\" style=\"height:18px\">
                                <div class=\"progress-bar bg-{$color}\" style=\"width:{$pct}%\">{$pct}%</div>
                            </div>";
                })
                ->addColumn('action', fn($t) => view('targets.buttons', ['target' => $t])->render())
                ->rawColumns(['achievement_pct', 'action'])
                ->make(true);
        }

        return view('targets.index');
    }
    public function create()
    {
        $this->authorizeSmOrAdmin();

        return view('targets.create', $this->formData());
    }
    public function store(Request $request)
    {
        $this->authorizeSmOrAdmin();

        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'target_amount' => 'required|numeric|min:1',
            'month'         => 'required|integer|min:1|max:12',
            'year'          => 'required|integer|min:2020|max:2099',
        ]);

        // One target per SP per month/year
        Target::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'month'   => $request->month,
                'year'    => $request->year,
            ],
            ['target_amount' => $request->target_amount]
        );

        return redirect()->route('targets.index')
            ->with('success', 'Target set successfully.');
    }
    public function show(Target $target)
    {
        $this->authorizeAccess($target);

        $target->load('user:id,username');

        // Achieved amount this month
        $achieved = Invoice::where('user_id', $target->user_id)
            ->where('status', Invoice::STATUS_APPROVED)
            ->whereMonth('invoice_date', $target->month)
            ->whereYear('invoice_date',  $target->year)
            ->sum('amount');

        $pct = $target->target_amount > 0
            ? round(($achieved / $target->target_amount) * 100)
            : 0;

        return view('targets.show', compact('target', 'achieved', 'pct'));
    }

    public function edit(Target $target)
    {
        $this->authorizeSmOrAdmin();

        return view('targets.edit', array_merge(
            $this->formData(),
            ['target' => $target]
        ));
    }
    public function update(Request $request, Target $target)
    {
        $this->authorizeSmOrAdmin();

        $request->validate([
            'target_amount' => 'required|numeric|min:1',
            'month'         => 'required|integer|min:1|max:12',
            'year'          => 'required|integer|min:2020|max:2099',
        ]);

        $target->update([
            'target_amount' => $request->target_amount,
            'month'         => $request->month,
            'year'          => $request->year,
        ]);

        return back()->with('success', 'Target updated.');
    }

    public function destroy(Target $target)
    {
        $this->authorizeSmOrAdmin();

        $target->delete();

        return redirect()->route('targets.index')
            ->with('success', 'Target deleted.');
    }

    private function formData(): array
    {
        $user = auth()->user();

        // SM sees only their SPs; Admin sees all SPs
        $spQuery = User::salesPersons()->active()->orderBy('username');

        if ($user->isSalesManager()) {
            $spQuery->where('reportive_id', $user->id);
        }

        return [
            'salesPersons' => $spQuery->get(['id', 'username']),
            'months'       => array_combine(range(1, 12), array_map(
                fn($m) => date('F', mktime(0, 0, 0, $m, 1)),
                range(1, 12)
            )),
            'years'        => array_combine(
                range(date('Y') - 1, date('Y') + 2),
                range(date('Y') - 1, date('Y') + 2)
            ),
        ];
    }

    private function authorizeSmOrAdmin(): void
    {
        if (!auth()->user()->isSalesManager() && !auth()->user()->isAdmin()) {
            abort(403, 'Only Sales Managers and Admins can manage targets.');
        }
    }

    private function authorizeAccess(Target $target): void
    {
        $user    = auth()->user();
        $teamIds = $user->teamMemberIds();

        if (!in_array($target->user_id, $teamIds)) {
            abort(403);
        }
    }
}
