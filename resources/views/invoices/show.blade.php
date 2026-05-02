@extends('layouts.dashboard')
@section('content')

    <header class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <div>
            <h5 class="mb-0">{{ $invoice->po_number }}</h5>
            <small class="text-muted">
                {{ $invoice->invoice_date->format('d M, Y') }} &bull;
                {{ $invoice->customer->name }}
            </small>
        </div>
        <div class="d-flex gap-2">
            @can('update', $invoice)
                @if ($invoice->isEditable())
                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-primary">
                        <i class="feather icon-edit-2 me-1"></i> Edit
                    </a>
                @endif
            @endcan

            @can('approve', $invoice)
                @if ($invoice->isSubmitted())
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approve-modal">
                        <i class="feather icon-check me-1"></i> Approve
                    </button>
                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#reject-modal">
                        <i class="feather icon-x me-1"></i> Reject
                    </button>
                @endif
            @endcan

            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                <i class="feather icon-printer me-1"></i> Print
            </button>
            <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-secondary">
                <i class="feather icon-arrow-left me-1"></i> Back
            </a>
        </div>
    </header>

    {{-- Rejection banner --}}
    @if ($invoice->isRejected())
        <div class="alert alert-danger d-print-none">
            <i class="feather icon-alert-circle me-2"></i>
            <strong>Rejected:</strong> {{ $invoice->rejection_reason }}
            @can('update', $invoice)
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-danger ms-2">Edit & Resubmit</a>
            @endcan
        </div>
    @endif

    @if ($invoice->isApproved())
        <div class="bg-white rounded shadow-sm p-4 mb-4">

            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                <h6 class="fw-bold mb-0">
                    <i class="feather icon-file-text me-2 text-primary"></i>
                    PO Document & Mail Status
                </h6>
                <div class="d-flex gap-2">

                    {{-- Download document --}}
                    <a href="{{ route('invoices.document.download', $invoice) }}" class="btn btn-sm btn-outline-primary">
                        <i class="feather icon-download me-1"></i> Download DOCX
                    </a>

                    {{-- Regenerate document (SM/Admin) --}}
                    @can('approve', $invoice)
                        <form action="{{ route('invoices.document.regenerate', $invoice) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Regenerate document? This will overwrite the stored file.')">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="feather icon-refresh-cw me-1"></i> Regenerate
                            </button>
                        </form>
                    @endcan

                </div>
            </div>

            {{-- Document status ─────────────────────────────────── --}}
            <div class="row g-3 mb-3">
                <div class="col-lg-4">
                    <div class="border rounded p-3 text-center">
                        @if ($invoice->po_document_path)
                            <i class="feather icon-check-circle fs-4 text-success mb-1 d-block"></i>
                            <small class="text-success fw-bold">Document Saved</small>
                            <p class="text-muted mb-0" style="font-size:0.7rem">
                                {{ $invoice->po_document_path }}
                            </p>
                        @else
                            <i class="feather icon-alert-circle fs-4 text-danger mb-1 d-block"></i>
                            <small class="text-danger fw-bold">Document Not Generated</small>
                            @can('approve', $invoice)
                                <form action="{{ route('invoices.document.regenerate', $invoice) }}" method="POST"
                                    class="mt-2">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs btn-danger btn-sm">Generate Now</button>
                                </form>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>

            {{-- Mail delivery status ────────────────────────────── --}}
            @can('approve', $invoice)
                <h6 class="small text-muted text-uppercase mb-2">Mail Delivery Status</h6>

                <div class="row g-3">

                    {{-- School --}}
                    <div class="col-lg-4">
                        <div class="border rounded p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold"><i class="feather icon-home me-1"></i>School</span>
                                @if ($invoice->school_mail_sent_at)
                                    <span class="badge bg-success">Sent</span>
                                @else
                                    <span class="badge bg-danger">Not Sent</span>
                                @endif
                            </div>
                            @if ($invoice->school_mail_sent_at)
                                <small class="text-muted">{{ $invoice->school_mail_sent_at->format('d M, Y h:i A') }}</small>
                            @else
                                <small class="text-muted">{{ $invoice->customer->email ?? 'No email on record' }}</small>
                            @endif
                            <form action="{{ route('invoices.mail.resend', $invoice) }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="to" value="school">
                                <button class="btn btn-xs btn-outline-primary btn-sm w-100"
                                    {{ !$invoice->customer->email ? 'disabled' : '' }}>
                                    <i class="feather icon-send me-1"></i> Resend
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- SP --}}
                    <div class="col-lg-4">
                        <div class="border rounded p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold"><i class="feather icon-user me-1"></i>Sales Person</span>
                                @if ($invoice->sp_mail_sent_at)
                                    <span class="badge bg-success">Sent</span>
                                @else
                                    <span class="badge bg-danger">Not Sent</span>
                                @endif
                            </div>
                            @if ($invoice->sp_mail_sent_at)
                                <small class="text-muted">{{ $invoice->sp_mail_sent_at->format('d M, Y h:i A') }}</small>
                            @else
                                <small class="text-muted">{{ $invoice->user->email ?? 'No email on record' }}</small>
                            @endif
                            <form action="{{ route('invoices.mail.resend', $invoice) }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="to" value="sp">
                                <button class="btn btn-xs btn-outline-primary btn-sm w-100"
                                    {{ !$invoice->user->email ? 'disabled' : '' }}>
                                    <i class="feather icon-send me-1"></i> Resend
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Accounts --}}
                    <div class="col-lg-4">
                        <div class="border rounded p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold"><i class="feather icon-book-open me-1"></i>Accounts Team</span>
                                @if ($invoice->accounts_mail_sent_at)
                                    <span class="badge bg-success">Sent</span>
                                @else
                                    <span class="badge bg-danger">Not Sent</span>
                                @endif
                            </div>
                            @if ($invoice->accounts_mail_sent_at)
                                <small class="text-muted">{{ $invoice->accounts_mail_sent_at->format('d M, Y h:i A') }}</small>
                            @else
                                <small class="text-muted">Accounts team members</small>
                            @endif
                            <form action="{{ route('invoices.mail.resend', $invoice) }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="to" value="accounts">
                                <button class="btn btn-xs btn-outline-primary btn-sm w-100">
                                    <i class="feather icon-send me-1"></i> Resend
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

                {{-- Resend All --}}
                <div class="text-end mt-3">
                    <form action="{{ route('invoices.mail.resend', $invoice) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Resend PO mail to all recipients?')">
                        @csrf
                        <input type="hidden" name="to" value="all">
                        <button class="btn btn-sm btn-primary">
                            <i class="feather icon-send me-1"></i> Resend to All
                        </button>
                    </form>
                </div>
            @endcan

        </div>
    @endif

    {{-- ═══ FINANCIAL SUMMARY (Hidden for Warehouse) ════════════════════════════════ --}}
    @if(auth()->user()->role?->name !== 'Warehouse')
    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['label' => 'PO Amount', 'val' => $invoice->amount, 'c' => 'warning'],
                ['label' => 'Billed', 'val' => $invoice->billing_amount, 'c' => 'info'],
                ['label' => 'Pending PO', 'val' => $invoice->amount - $invoice->billing_amount, 'c' => 'secondary'],
                ['label' => 'Collected', 'val' => $invoice->collected_amount, 'c' => 'success'],
                ['label' => 'Outstanding', 'val' => $invoice->outstanding_amount, 'c' => 'danger'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="col-6 col-lg">
                <div class="bg-white rounded shadow-sm p-3 text-center border-top border-{{ $card['c'] }} border-3">
                    <h5 class="fw-bold text-{{ $card['c'] }} mb-0">₹{{ number_format($card['val'], 2) }}</h5>
                    <small class="text-muted">{{ $card['label'] }}</small>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <div class="row g-3">

        {{-- ═══ PO DETAILS ════════════════════════════════════ --}}
        <div class="col-lg-5">
            <div class="bg-white rounded shadow-sm p-4 mb-3">
                <h6 class="fw-bold border-bottom pb-2 mb-3">Order Details</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <th class="text-muted" style="width:40%">PO Number</th>
                        <td><strong>{{ $invoice->po_number }}</strong></td>
                    </tr>
                    <tr>
                        <th class="text-muted">PO Date</th>
                        <td>{{ $invoice->invoice_date->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Delivery Due</th>
                        <td>{{ $invoice->delivery_due_date ? $invoice->delivery_due_date->format('d M, Y') : '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status</th>
                        <td>
                            @php $c = ['approved'=>'success','submitted'=>'warning','draft'=>'secondary','rejected'=>'danger'][$invoice->status]??'secondary'; @endphp
                            <span class="badge bg-{{ $c }}">{{ ucfirst($invoice->status) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Sales Person</th>
                        <td>{{ $invoice->user->username }}</td>
                    </tr>
                    @if ($invoice->approvedBy)
                        <tr>
                            <th class="text-muted">Approved By</th>
                            <td>{{ $invoice->approvedBy->username }} <small
                                    class="text-muted">({{ $invoice->approved_at->format('d M, Y') }})</small></td>
                        </tr>
                    @endif
                    <tr>
                        <th class="text-muted">School</th>
                        <td>{{ $invoice->customer->name }}<br><small
                                class="text-muted">{{ $invoice->customer->school_code }}</small></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Phone</th>
                        <td>{{ $invoice->phone_number ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Address</th>
                        <td>{{ $invoice->address ?? '—' }}</td>
                    </tr>
                </table>
            </div>

            {{-- PDC --}}
            @if ($invoice->pdcs->count())
                <div class="bg-white rounded shadow-sm p-4 mb-3">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">PDC Cheques</h6>
                    @foreach ($invoice->pdcs as $pdc)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <strong>{{ $pdc->pdc_label }}</strong>
                                <small class="text-muted d-block">{{ $pdc->cheque_number }} —
                                    {{ $pdc->bank_name }}</small>
                                <small class="text-muted">{{ $pdc->cheque_date->format('d M, Y') }}</small>
                            </div>
                            <div class="text-end">
                                <strong>₹{{ number_format($pdc->amount, 2) }}</strong>
                                <small
                                    class="badge bg-{{ ['pending' => 'warning', 'cleared' => 'success', 'bounced' => 'danger'][$pdc->status] }} d-block mt-1">
                                    {{ ucfirst($pdc->status) }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ═══ ORDER ITEMS + COLLECTION HISTORY ══════════════ --}}
        <div class="col-lg-7">

            {{-- Items table --}}
            <div class="bg-white rounded shadow-sm p-4 mb-3">
                <h6 class="fw-bold border-bottom pb-2 mb-3">Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th class="text-end">Qty</th>
                                @if(auth()->user()->role?->name !== 'Warehouse')
                                <th class="text-end">Rate</th>
                                <th class="text-end">Amount</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->invoiceItems as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        {{ $item->product->name }}
                                        @if ($item->product->category)
                                            <small class="text-muted d-block">{{ $item->product->category->name }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ $item->quantity }}</td>
                                    @if(auth()->user()->role?->name !== 'Warehouse')
                                    <td class="text-end">₹{{ number_format($item->rate, 2) }}</td>
                                    <td class="text-end">₹{{ number_format($item->amount, 2) }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        @if(auth()->user()->role?->name !== 'Warehouse')
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">Total PO Amount</td>
                                <td class="text-end">₹{{ number_format($invoice->amount, 2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Collection history (Hidden for Warehouse) --}}
            @if(auth()->user()->role?->name !== 'Warehouse')
            @if ($invoice->collections->count())
                <div class="bg-white rounded shadow-sm p-4 mb-3">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Collection History</h6>
                    @foreach ($invoice->collections as $col)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <strong>{{ ucfirst($col->payment_mode) }}</strong>
                                @if ($col->reference_number)
                                    <small class="text-muted d-block">Ref: {{ $col->reference_number }}</small>
                                @endif
                                <small class="text-muted">{{ $col->collected_at->format('d M, Y') }} by
                                    {{ $col->collectedBy->username }}</small>
                            </div>
                            <strong class="text-success">₹{{ number_format($col->collected_amount, 2) }}</strong>
                        </div>
                    @endforeach
                    <div class="d-flex justify-content-between pt-2 fw-bold">
                        <span>Total Collected</span>
                        <span class="text-success">₹{{ number_format($invoice->collected_amount, 2) }}</span>
                    </div>
                </div>
            @endif

            @include('invoices.partials.po-log', ['invoice' => $invoice])
            @endif

            @include('invoices.partials.dispatch-history', ['invoice' => $invoice])

            {{-- Remarks / Terms --}}
            @if ($invoice->remarks || $invoice->terms)
                <div class="bg-white rounded shadow-sm p-4">
                    @if ($invoice->remarks)
                        <h6 class="fw-bold mb-1">Remarks</h6>
                        <p class="text-muted small">{{ $invoice->remarks }}</p>
                    @endif
                    @if ($invoice->terms)
                        <h6 class="fw-bold mb-1 mt-3">Terms & Conditions</h6>
                        <p class="text-muted small">{{ $invoice->terms }}</p>
                    @endif
                </div>
            @endif

        </div>
    </div>


    {{-- ═══ APPROVE MODAL ══════════════════════════════════════ --}}
    @can('approve', $invoice)
        @if ($invoice->isSubmitted())
            <div class="modal fade" id="approve-modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Approve {{ $invoice->po_number }}</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Approve this PO for <strong>{{ $invoice->customer->name }}</strong>?</p>
                            <p class="text-muted small">A copy will be emailed to the school, SP, and Accounts team.</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('invoices.approve', $invoice) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-success">Confirm Approve</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="reject-modal" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('invoices.reject', $invoice) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-danger">Reject {{ $invoice->po_number }}</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                <textarea name="rejection_reason" class="form-control" rows="4" required minlength="20"
                                    placeholder="Minimum 20 characters — explain what needs correction..."></textarea>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-danger">Reject & Return to SP</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endcan

@endsection
