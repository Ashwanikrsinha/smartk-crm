<?php

namespace App\Http\Controllers;

use App\Exports\WarehouseDispatchExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class WarehouseExportController extends Controller
{
    public function __invoke(Request $request)
    {
        // Only Warehouse (and Admin) can export
        $user = auth()->user();
        abort_unless(
            $user->role?->name === 'Warehouse' || $user->isAdmin(),
            403
        );

        $filters = $request->only([
            'scope',
            'year',
            'month',
            'date_from',
            'date_to',
            'due_date_from',
            'due_date_to',
            'product_type',
            'product_id',
        ]);

        $scope = $request->input('scope', 'pending');
        $filters['scope'] = $scope;

        $prefix = $scope === 'all' ? 'all-dispatches-' : 'pending-dispatch-queue-';
        $filename = $prefix . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(new WarehouseDispatchExport($filters), $filename);
    }
}
