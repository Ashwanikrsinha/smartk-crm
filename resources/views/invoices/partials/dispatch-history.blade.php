@if ($invoice->isApproved())
    <div class="bg-white rounded shadow-sm p-4 mb-4">

        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h6 class="fw-bold mb-0">
                <i class="feather icon-truck me-2 text-primary"></i>
                Dispatch History
                <span class="badge bg-secondary ms-1">{{ $invoice->dispatches->count() }}</span>
            </h6>
            @if (auth()->user()->hasPermission('browse_dispatch_queue'))
                <a href="{{ route('dispatches.create', ['invoice_id' => $invoice->id]) }}"
                    class="btn btn-sm btn-outline-primary">
                    <i class="feather icon-plus me-1"></i> New Dispatch
                </a>
            @endif
        </div>

        {{-- Per-item dispatch summary ───────────────────────── --}}
        <div class="mb-4">
            <h6 class="text-muted small text-uppercase mb-2">Item-wise Dispatch Status</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Category</th>
                            <th>Product</th>
                            <th class="text-end">Ordered</th>
                            <th class="text-end text-success">Dispatched</th>
                            <th class="text-end text-danger">Remaining</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->invoiceItems as $item)
                            @php
                                $done = $invoice->dispatches->flatMap->items
                                    ->where('invoice_item_id', $item->id)
                                    ->sum('quantity_dispatched');
                                $pct = $item->quantity > 0 ? round(($done / $item->quantity) * 100) : 0;
                                $remaining = max($item->quantity - $done, 0);
                            @endphp
                            <tr>
                                <td>{{ $item->product->category?->name ?? '—' }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end text-success fw-bold">{{ $done }}</td>
                                <td class="text-end {{ $remaining > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                    {{ $remaining }}
                                </td>
                                <td style="min-width:120px">
                                    <div class="progress" style="height:16px">
                                        <div class="progress-bar bg-{{ $pct >= 100 ? 'success' : ($pct > 0 ? 'primary' : 'secondary') }}"
                                            style="width:{{ $pct }}%">
                                            {{ $pct }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Individual dispatch records ─────────────────────── --}}
        @if ($invoice->dispatches->count())
            <h6 class="text-muted small text-uppercase mb-2">Individual Dispatches</h6>
            @foreach ($invoice->dispatches->sortByDesc('dispatch_date') as $dispatch)
                <div class="border rounded p-3 mb-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>{{ $dispatch->dispatch_number }}</strong>
                            <span class="text-muted ms-2 small">
                                {{ $dispatch->dispatch_date->format('d M, Y') }}
                            </span>
                            <span class="text-muted ms-2 small">
                                by {{ $dispatch->dispatchedBy->username }}
                            </span>
                        </div>
                        <a href="{{ route('dispatches.show', $dispatch) }}"
                            class="btn btn-xs btn-outline-primary btn-sm">
                            View Details
                        </a>
                    </div>

                    {{-- Items in this dispatch --}}
                    <div class="mt-2">
                        @foreach ($dispatch->items as $di)
                            <span class="badge bg-light text-dark border me-1 mb-1">
                                {{ $di->product->category?->name ?? '' }}
                                {{ $di->product->name }} — {{ $di->quantity_dispatched }} units
                            </span>
                        @endforeach
                    </div>

                    {{-- Transport info if available --}}
                    @if ($dispatch->bilty_number || $dispatch->vehicle_number || $dispatch->challan_number)
                        <div class="mt-2 text-muted small">
                            @if ($dispatch->bilty_number)
                                <span class="me-3">Bilty: {{ $dispatch->bilty_number }}</span>
                            @endif
                            @if ($dispatch->challan_number)
                                <span class="me-3">Challan: {{ $dispatch->challan_number }}</span>
                            @endif
                            @if ($dispatch->vehicle_number)
                                <span class="me-3">Vehicle: {{ $dispatch->vehicle_number }}</span>
                            @endif
                            @if ($dispatch->driver_name)
                                <span>Driver: {{ $dispatch->driver_name }}
                                    @if ($dispatch->driver_phone)
                                        ({{ $dispatch->driver_phone }})
                                    @endif
                                </span>
                            @endif
                        </div>
                    @endif

                    @if ($dispatch->remarks)
                        <div class="mt-1 text-muted small">Note: {{ $dispatch->remarks }}</div>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-muted text-center py-2 mb-0">No dispatches recorded yet for this PO.</p>
        @endif

    </div>
@endif
