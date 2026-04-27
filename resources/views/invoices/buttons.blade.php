{{-- View --}}
@can('view', $invoice)
    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm text-primary" title="View PO">
        <i class="feather icon-eye"></i>
    </a>
@endcan

{{-- Edit — only if editable (draft/rejected) --}}
@can('update', $invoice)
    @if ($invoice->isEditable())
        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm text-primary" title="Edit PO">
            <i class="feather icon-edit-2"></i>
        </a>
    @endif
@endcan

{{-- Approve / Reject — SM only, submitted POs only --}}
@can('approve', $invoice)
    @if ($invoice->isSubmitted())
        <button type="button" class="btn btn-sm text-success" title="Approve" data-bs-toggle="modal"
            data-bs-target="#approve-modal-{{ $invoice->id }}">
            <i class="feather icon-check-circle"></i>
        </button>

        <button type="button" class="btn btn-sm text-danger" title="Reject" data-bs-toggle="modal"
            data-bs-target="#reject-modal-{{ $invoice->id }}">
            <i class="feather icon-x-circle"></i>
        </button>

        {{-- Approve Modal --}}
        <div class="modal fade" id="approve-modal-{{ $invoice->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Approve PO {{ $invoice->po_number }}</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Approve this Purchase Order from <strong>{{ $invoice->user->username }}</strong> for
                            <strong>{{ $invoice->customer->name }}</strong>?</p>
                        <p class="text-muted small">On approval, a copy will be sent to the school, SP, and Accounts team.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('invoices.approve', $invoice) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-success">Confirm Approve</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reject Modal --}}
        <div class="modal fade" id="reject-modal-{{ $invoice->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('invoices.reject', $invoice) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header">
                            <h5 class="modal-title text-danger">Reject PO {{ $invoice->po_number }}</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="4"
                                placeholder="Explain what needs to be corrected (minimum 20 characters)..." required minlength="20"></textarea>
                            <small class="text-muted">This will be shown to the SP so they can correct and resubmit.</small>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-danger">Reject & Return to SP</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endcan

{{-- Delete --}}
@can('delete', $invoice)
    <form class="d-inline-block" action="{{ route('invoices.destroy', $invoice) }}" method="POST"
        onsubmit="return confirm('Delete PO {{ $invoice->po_number }}? This cannot be undone.')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm text-danger" title="Delete">
            <i class="feather icon-trash-2"></i>
        </button>
    </form>
@endcan
