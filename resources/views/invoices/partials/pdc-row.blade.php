<div class="pdc-row border rounded p-3 mb-2 d-flex align-items-center gap-3 flex-wrap">

    <span class="badge bg-success">PDC {{ $idx + 1 }}</span>

    <div>
        <label class="form-label mb-1 small">Date <span class="text-danger">*</span></label>
        <input type="date" name="pdc_dates[]" class="form-control form-control-sm" style="width:140px"
            value="{{ isset($pdc) ? $pdc->cheque_date->format('Y-m-d') : '' }}">
    </div>

    <div>
        <label class="form-label mb-1 small">Cheque Number</label>
        <input type="text" name="pdc_cheque_numbers[]" class="form-control form-control-sm" style="width:160px"
            placeholder="Cheque no." value="{{ isset($pdc) ? $pdc->cheque_number : '' }}">
    </div>

    <div>
        <label class="form-label mb-1 small">Bank Name</label>
        <input type="text" name="pdc_bank_names[]" class="form-control form-control-sm" style="width:160px"
            placeholder="Bank name" value="{{ isset($pdc) ? $pdc->bank_name : '' }}">
    </div>

    <div>
        <label class="form-label mb-1 small">Amount (₹)</label>
        <input type="number" step="0.01" name="pdc_amounts[]" class="form-control form-control-sm"
            style="width:120px" placeholder="0.00" value="{{ isset($pdc) ? $pdc->amount : '' }}">
    </div>

    @if ($idx > 0)
        <button type="button" class="btn btn-sm btn-outline-danger remove-pdc-row ms-auto mt-3">
            <i class="feather icon-trash-2"></i>
        </button>
    @else
        <div class="ms-auto mt-3" style="width:34px"></div>
    @endif

</div>
