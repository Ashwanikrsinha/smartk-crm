{{-- View --}}
@can('view', $invoice)
    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm text-primary" title="View PO">
        <i class="feather icon-eye"></i>
    </a>
@endcan

{{-- Edit — SP in draft/rejected/bm_rejected, SM before BM approval --}}
@can('update', $invoice)
    @if ($invoice->isEditable() || ($invoice->isSmApproved() && auth()->user()->isSalesManager()))
        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm text-primary" title="Edit PO">
            <i class="feather icon-edit-2"></i>
        </a>
    @endif
@endcan

{{-- ═══ SM APPROVE / REJECT (submitted POs only) ═══════ --}}
@can('approve', $invoice)
    @if ($invoice->isSubmitted() && (auth()->user()->isSalesManager() || auth()->user()->isAdmin()))
        <button type="button" class="btn btn-sm text-success" title="SM Approve" data-bs-toggle="modal"
            data-bs-target="#sm-approve-modal-{{ $invoice->id }}">
            <i class="feather icon-check-circle"></i>
        </button>

        <button type="button" class="btn btn-sm text-danger" title="SM Reject" data-bs-toggle="modal"
            data-bs-target="#sm-reject-modal-{{ $invoice->id }}">
            <i class="feather icon-x-circle"></i>
        </button>

        {{-- SM Approve Modal --}}
        <div class="modal fade" id="sm-approve-modal-{{ $invoice->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">SM Approve — {{ $invoice->po_number }}</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Approve this PO from <strong>{{ $invoice->user->username }}</strong>
                            for <strong>{{ $invoice->customer->name }}</strong>?</p>
                        <div class="alert alert-info py-2 mb-0">
                            <i class="feather icon-info me-1"></i>
                            After SM approval, the PO moves to <strong>Pending BM</strong> status.
                            The Business Manager will give final approval.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('invoices.approve', $invoice) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-success">SM Approve → Send to BM</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- SM Reject Modal --}}
        <div class="modal fade" id="sm-reject-modal-{{ $invoice->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('invoices.reject', $invoice) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger">SM Reject — {{ $invoice->po_number }}</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="4" required minlength="20"
                                placeholder="Minimum 20 characters — explain what needs correction..."></textarea>
                            <small class="text-muted">SP will see this reason and can edit & resubmit.</small>
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

{{-- ═══ BM APPROVE / REJECT (sm_approved POs only) ══════ --}}
@can('approve', $invoice)
    @if ($invoice->isSmApproved() && (auth()->user()->isBusinessManager() || auth()->user()->isAdmin()))
        <button type="button" class="btn btn-sm btn-success" title="BM Final Approve" data-bs-toggle="modal"
            data-bs-target="#bm-approve-modal-{{ $invoice->id }}">
            <i class="feather icon-check-circle"></i> Final
        </button>

        <button type="button" class="btn btn-sm btn-outline-danger" title="BM Reject" data-bs-toggle="modal"
            data-bs-target="#bm-reject-modal-{{ $invoice->id }}">
            <i class="feather icon-x-circle"></i>
        </button>

        {{-- BM Approve Modal --}}
        <div class="modal fade" id="bm-approve-modal-{{ $invoice->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background:#0F2044">
                        <h5 class="modal-title text-white">
                            <i class="feather icon-check-circle me-2 text-success"></i>
                            Final BM Approval — {{ $invoice->po_number }}
                        </h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th class="text-muted">School</th>
                                <td>{{ $invoice->customer->name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">SP</th>
                                <td>{{ $invoice->user->username }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">SM Approved By</th>
                                <td>{{ $invoice->approvedBy?->username ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">PO Amount</th>
                                <td><strong>₹{{ number_format($invoice->amount, 2) }}</strong></td>
                            </tr>
                        </table>
                        <div class="alert alert-success py-2 mb-0">
                            <i class="feather icon-info me-1"></i>
                            On final approval: document generated, mail sent to school + SP + Accounts.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('invoices.bm-approve', $invoice) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-success fw-bold">
                                <i class="feather icon-check me-1"></i> Final Approve
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- BM Reject Modal --}}
        <div class="modal fade" id="bm-reject-modal-{{ $invoice->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('invoices.bm-reject', $invoice) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger">BM Return — {{ $invoice->po_number }}</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label">Reason for Return <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="4" required minlength="20"
                                placeholder="Minimum 20 characters — explain what needs correction..."></textarea>
                            <small class="text-muted">SM and SP will be notified with this reason.</small>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-danger">Return for Correction</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endcan

{{-- Delete --}}
@can('delete', $invoice)
    <form class="d-inline-block" action="{{ route('invoices.destroy', $invoice) }}" method="POST"
        onsubmit="return confirm('Delete PO {{ $invoice->po_number }}?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm text-danger" title="Delete">
            <i class="feather icon-trash-2"></i>
        </button>
    </form>
@endcan
