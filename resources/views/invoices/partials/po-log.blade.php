<div class="bg-white rounded shadow-sm p-4 mb-4">

    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
        <h6 class="fw-bold mb-0">
            <i class="feather icon-activity me-2 text-primary"></i>
            PO Activity Log
            <span class="badge bg-secondary ms-1">{{ $invoice->logs->count() }}</span>
        </h6>
        <a href="{{ route('reports.po-log-export', $invoice->id) }}"
           class="btn btn-sm btn-outline-success">
            <i class="feather icon-download me-1"></i> Export Log
        </a>
    </div>

    @if($invoice->logs->count())

    <div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr>
                <th>Date & Time</th>
                <th>Action</th>
                <th>By</th>
                <th class="text-end">Amount</th>
                <th>Ref. No.</th>
                <th class="text-end"> PO Amt</th>
                <th class="text-end"> Billed</th>
                <th class="text-end"> Pend. PO</th>
                <th class="text-end"> Collected</th>
                <th class="text-end"> Outstanding</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->logs->sortByDesc('created_at') as $log)
            <tr>
                <td class="small text-muted">
                    {{ $log->created_at->format('d M, Y') }}<br>
                    <span style="font-size:0.75rem">{{ $log->created_at->format('h:i A') }}</span>
                </td>
                <td>
                    <span class="badge bg-{{ $log->action_color }}">
                        {{ $log->action_label }}
                    </span>
                    @if($log->billing_source === 'manual')
                    <span class="badge bg-light text-muted ms-1" style="font-size:0.7rem">Tally</span>
                    @elseif($log->billing_source === 'crm')
                    <span class="badge bg-light text-muted ms-1" style="font-size:0.7rem">CRM</span>
                    @endif
                </td>
                <td class="small">{{ $log->user->username }}</td>
                <td class="text-end fw-bold">
                    @if($log->amount > 0)
                    ₹{{ number_format($log->amount, 2) }}
                    @else —
                    @endif
                </td>
                <td class="small text-muted">{{ $log->reference_number ?? '—' }}</td>
                <td class="text-end small">₹{{ number_format($log->snapshot_po_amount, 0) }}</td>
                <td class="text-end small text-info">₹{{ number_format($log->snapshot_billed_amount, 0) }}</td>
                <td class="text-end small">₹{{ number_format($log->snapshot_pending_po, 0) }}</td>
                <td class="text-end small text-success">₹{{ number_format($log->snapshot_collected, 0) }}</td>
                <td class="text-end small {{ $log->snapshot_outstanding > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                    ₹{{ number_format($log->snapshot_outstanding, 0) }}
                </td>
                <td class="small text-muted">{{ $log->remarks ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    @else
    <p class="text-muted text-center py-3 mb-0">No log entries yet for this PO.</p>
    @endif

</div>
