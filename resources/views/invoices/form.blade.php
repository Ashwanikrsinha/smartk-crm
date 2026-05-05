@csrf

{{-- ═══════════════════════════════════════════════════
     SECTION 1: SCHOOL SELECTION
═══════════════════════════════════════════════════ --}}
<div class="bg-white rounded shadow-sm p-4 mb-4">

    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
        <h6 class="fw-bold mb-0"><i class="feather icon-home me-2 text-primary"></i>School Details</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
            data-bs-target="#new-school-modal">
            <i class="feather icon-plus me-1"></i> New School
        </button>
    </div>

    <div class="row">

        {{-- School Search --}}
        <div class="col-lg-6 mb-3">
            <label class="form-label">School <span class="text-danger">*</span></label>
            <select name="customer_id" id="school-select" class="form-control" required>
                <option value="">Search and select school...</option>
                @foreach ($customers as $school)
                    <option value="{{ $school->id }}"
                        {{ isset($invoice) && $invoice->customer_id == $school->id ? 'selected' : '' }}>
                        [{{ $school->school_code }}] {{ $school->name }} — {{ $school->city }}, {{ $school->state }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- School Code (auto-filled, read-only) --}}
        <div class="col-lg-3 mb-3">
            <label class="form-label">School Code</label>
            <input type="text" class="form-control bg-light" id="school-code" readonly
                value="{{ isset($invoice) ? $invoice->customer->school_code : '' }}">
        </div>

        {{-- Lead From --}}
        {{-- <div class="col-lg-3 mb-3">
            <label class="form-label">Lead From</label>
            <select name="lead_source_id" id="lead-source" class="form-control">
                <option value="">Select lead source...</option>
                @foreach ($lead_sources as $id => $name)
                    <option value="{{ $id }}"
                        {{ isset($invoice) && $invoice->customer->lead_source_id == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div> --}}
        <input type="hidden" name="lead_source_id" id="lead-source"
            value="{{ $invoice->customer->lead_source_id ?? old('lead_source_id') }}">
        <div class="col-lg-3 mb-3">
            <label class="form-label">Lead Source</label>
            <input type="text" disabled name="lead_source_name" class="form-control" id="lead-source-name"
                value="{{ isset($invoice) ? $invoice->lead_source_name : old('lead_source_name') }}"
                placeholder="Lead source name">
        </div>

        {{-- Contact Person --}}
        <div class="col-lg-4 mb-3">
            <label class="form-label">Contact Person Name</label>
            <input type="text" name="contact_person" class="form-control" id="contact-person"
                value="{{ isset($invoice) ? $invoice->contact_person : old('contact_person') }}"
                placeholder="Principal / Coordinator name">
        </div>

        {{-- Phone --}}
        <div class="col-lg-4 mb-3">
            <label class="form-label">Mobile Number</label>
            <input type="text" name="phone_number" class="form-control" id="school-phone"
                value="{{ isset($invoice) ? $invoice->phone_number : old('phone_number') }}"
                placeholder="10-digit mobile number">
        </div>

        {{-- Email --}}
        <div class="col-lg-4 mb-3">
            <label class="form-label">Email ID</label>
            <input type="email" name="email" class="form-control" id="school-email"
                value="{{ isset($invoice) ? $invoice->customer->email : old('email') }}"
                placeholder="school@example.com">
        </div>

        {{-- Date --}}
        <div class="col-lg-3 mb-3">
            <label class="form-label">PO Date <span class="text-danger">*</span></label>
            <input type="date" name="invoice_date" class="form-control" required
                value="{{ isset($invoice) ? $invoice->invoice_date->format('Y-m-d') : date('Y-m-d') }}">
        </div>

        {{-- Delivery Due Date --}}
        <div class="col-lg-3 mb-3">
            <label class="form-label">Delivery Due Date</label>
            <input type="date" name="delivery_due_date" class="form-control"
                value="{{ isset($invoice) ? optional($invoice->delivery_due_date)->format('Y-m-d') : '' }}">
        </div>

        {{-- Address --}}
        <div class="col-lg-6 mb-3">
            <label class="form-label">School Address</label>
            <input type="text" name="address" class="form-control" id="school-address"
                value="{{ isset($invoice) ? $invoice->address : old('address') }}" placeholder="Full address">
        </div>

        {{-- State --}}
        <div class="col-lg-3 mb-3">
            <label class="form-label">State</label>
            <input type="text" name="state" class="form-control" id="school-state"
                value="{{ isset($invoice) ? $invoice->customer->state : old('state') }}" placeholder="State">
        </div>

        {{-- City --}}
        <div class="col-lg-3 mb-3">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" id="school-city"
                value="{{ isset($invoice) ? $invoice->customer->city : old('city') }}" placeholder="City">
        </div>

        {{-- Pin Code --}}
        <div class="col-lg-2 mb-3">
            <label class="form-label">Pin Code</label>
            <input type="text" name="pin_code" class="form-control" id="school-pin"
                value="{{ isset($invoice) ? $invoice->customer->pin_code : old('pin_code') }}"
                placeholder="6-digit pin">
        </div>
        {{-- @if (!auth()->user()->isSalesPerson())
            <div class="col-lg-4 mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select name="user_id" id="user-select" class="form-control" required>
                    <option value="">Search and select employee...</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"
                            {{ isset($invoice) && $invoice->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->username }} ({{ $user->emp_code }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif --}}

        {{-- Visit (optional) --}}
        <div class="col-lg-4 mb-3">
            <label class="form-label">Link Visit <small class="text-muted">(optional)</small></label>
            <select name="visit_id" id="visit-select" class="form-control">
                <option value="">Select visit (optional)...</option>
                {{-- Populated via AJAX when school is chosen --}}
                @if (isset($invoice) && $invoice->visit_id)
                    <option value="{{ $invoice->visit_id }}" selected>
                        V-{{ $invoice->visit->visit_number }}
                    </option>
                @endif
            </select>
        </div>

    </div>
</div>


{{-- ═══════════════════════════════════════════════════
     SECTION 2: ORDER ITEMS
═══════════════════════════════════════════════════ --}}
<div class="bg-white rounded shadow-sm p-4 mb-4">

    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
        <h6 class="fw-bold mb-0"><i class="feather icon-package me-2 text-primary"></i>Order Items</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-row">
            <i class="feather icon-plus me-1"></i> Add Row
        </button>
    </div>

    {{-- Table Header --}}
    <div class="table-responsive">
        <table class="table table-bordered" id="items-table">
            <thead class="table-light">
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:18%">Product Type</th>
                    <th style="width:18%">Product / Kit</th>
                    <th style="width:9%">MRP</th>
                    <th style="width:8%">Qty</th>
                    <th style="width:9%">Discount (%)</th>
                    <th style="width:12%">Net Sale Price</th>
                    <th style="width:12%">Total Amount</th>
                    <th style="width:5%"></th>
                </tr>
            </thead>
            <tbody id="items-body">

                @if (isset($invoice) && $invoice->invoiceItems->count())

                    {{-- Edit mode: load existing items --}}
                    @foreach ($invoice->invoiceItems as $idx => $item)
                        @include('invoices.partials.item-row', [
                            'idx' => $idx,
                            'item' => $item,
                            'categories' => $categories,
                        ])
                    @endforeach
                @else
                    {{-- Create mode: one blank row --}}
                    @include('invoices.partials.item-row', [
                        'idx' => 0,
                        'item' => null,
                        'categories' => $categories,
                    ])
                @endif

            </tbody>
        </table>
    </div>

    {{-- Total PO Amount --}}
    <div class="d-flex justify-content-end mt-2">
        <div class="text-end">
            <label class="form-label fw-bold">Total PO Amount</label>
            <div class="input-group" style="width: 200px;">
                <span class="input-group-text">₹</span>
                <input type="text" class="form-control fw-bold text-end" id="total-po-amount-display" readonly
                    placeholder="0.00">
                <input type="hidden" name="total_po_amount" id="total-po-amount">
            </div>
        </div>
    </div>

</div>


{{-- ═══════════════════════════════════════════════════
     SECTION 3: PDC (POST DATED CHEQUES)
═══════════════════════════════════════════════════ --}}
<div class="bg-white rounded shadow-sm p-4 mb-4">

    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
        <h6 class="fw-bold mb-0">
            <i class="feather icon-credit-card me-2 text-primary"></i>
            Payment Cheques (PDC)
            <small class="text-muted fw-normal ms-1">— Post dated cheques from school</small>
        </h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-pdc-row">
            <i class="feather icon-plus me-1"></i> Add Cheque
        </button>
    </div>

    <div id="pdc-body">

        @if (isset($invoice) && $invoice->pdcs->count())
            @foreach ($invoice->pdcs as $idx => $pdc)
                @include('invoices.partials.pdc-row', ['idx' => $idx, 'pdc' => $pdc])
            @endforeach
        @else
            @include('invoices.partials.pdc-row', ['idx' => 0, 'pdc' => null])
        @endif

    </div>

</div>


{{-- ═══════════════════════════════════════════════════
     SECTION 4: REMARKS & TERMS
═══════════════════════════════════════════════════ --}}
<div class="bg-white rounded shadow-sm p-4 mb-4">
    <h6 class="fw-bold border-bottom pb-2 mb-3">
        <i class="feather icon-file-text me-2 text-primary"></i>Additional Details
    </h6>
    <div class="row">
        <div class="col-lg-12 mb-3">
            <label class="form-label">Remarks</label>
            <textarea name="remarks" class="form-control" rows="3" placeholder="Any notes or special instructions...">{{ isset($invoice) ? $invoice->remarks : old('remarks') }}</textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mb-3">
            <label class="form-label">Terms & Conditions</label>
            <textarea name="terms" readonly class="form-control" rows="6"
                placeholder="Payment terms, delivery conditions...">{{ isset($invoice)
                    ? $invoice->terms
                    : '             1. S. Chand Edutech Pvt. Ltd. would retain the License to the product given and it is
                not to be copied, modified, translated, decompiled, or otherwise used in any other
                manner than for teaching children in the school.
                2. The Educational Institute can use the program only at its location set out in the
                Order Form.
                3. In the event of education institute failing to make the payment to Edutech mutually
                agreed on in the Purchase order, Edutech remedies include terminating this PO
                without notice and recalling the material already delivered under the PO. If the

                outstanding amount is not paid, even after the mutually agreed T&amp;C, interest of 2%
                per month is paid in full.
                4. Edutech shall not be held responsible or liable for not performing any of its
                obligations or undertakings provided for in this form if such performance is
                prevented, delayed or hindered by an act of god, fire, flood, explosion, riots, inability
                to procure labour, equipments, facilities, supplies, failure of transportation, strikes,
                lock outs not within the reasonable control of Edutech.
                5. All payments shall be non-refundable once made.
                6. Once order is received, number of kits ordered cannot be reduced.
                7. All the outstanding payments must be cleared within 3 Months of order delivery.
                8. In case of any dishonour of a PDC, S. Chand Edutech shall immediately bring the
                matter to the knowledge of the school.
                9. The school shall take immediate steps to ensure that the reason for dishonour is
                removed &amp; intimate S. Chand Edutech to represent the cheque again within 2
                working days or remit the amount via RTGS/NEFT.
                10. Cheque dishonouring charges shall be borne by the school.
                11. Any missing material needs to be notified by the school within 15 working days after
                receiving the material, after that if the school notify us then the school will pay the
                delivery charges as well the missing material charges.' }}</textarea>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════
     SECTION 5: ACTIONS
═══════════════════════════════════════════════════ --}}
<div class="bg-white rounded shadow-sm p-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
    <div class="d-flex gap-2">
        <button type="submit" name="action" value="draft" class="btn btn-outline-primary">
            <i class="feather icon-save me-1"></i> Save as Draft
        </button>
        <button type="submit" name="action" value="submit" class="btn btn-primary"
            onclick="return confirm('Submit this PO to your Sales Manager for approval?')">
            <i class="feather icon-send me-1"></i> Submit for Approval
        </button>
    </div>
</div>


@push('scripts')
    <script>
        $(document).ready(function() {

            // ─── Selectize school dropdown ───────────────────────
            const schoolSelect = $('#school-select').selectize({
                placeholder: 'Search school by name or code...',
                onChange: function(schoolId) {
                    if (!schoolId) return;
                    fetchSchoolDetails(schoolId);
                    fetchVisits(schoolId);
                }
            });
            const userSelect = $('#user-select').selectize({
                placeholder: 'Search employee by name or code...',
                onChange: function(userId) {
                    const schoolId = $('#school-select').val();
                    if (!userId) return;
                    if (!schoolId) return;
                    fetchVisits(schoolId, userId);
                }
            });

            // ─── Selectize visit dropdown ────────────────────────
            $('#visit-select').selectize({
                placeholder: 'Select visit (optional)...'
            });
            $('#user-select').selectize({
                placeholder: 'Select employee ...'
            });


            // ─── Fetch school details via AJAX ──────────────────
            function fetchSchoolDetails(schoolId) {
                $.get(`/invoices/school/${schoolId}`, function(data) {
                    $('#school-code').val(data.school_code);
                    $('#school-phone').val(data.phone_number);
                    $('#school-address').val(data.address);
                    $('#school-state').val(data.state);
                    $('#school-city').val(data.city);
                    $('#school-pin').val(data.pin_code);
                    $('#school-email').val(data.email);
                    $('#lead-source-name').val(data.lead_source_name);
                    $('#lead-source').val(data.lead_source_id);

                    // Set lead source
                    // const leadSelectize = $('#lead-source')[0].selectize;
                    // if (leadSelectize && data.lead_source_id) {
                    //     leadSelectize.setValue(data.lead_source_id);
                    // }
                });
            }

            // ─── Fetch visits for this school ───────────────────
            function fetchVisits(schoolId, userId) {
                $.get(`/invoices/visits/${schoolId}?user_id=${userId}`, function(visits) {
                    const visitSelectize = $('#visit-select')[0].selectize;
                    visitSelectize.clearOptions();
                    visitSelectize.addOption({
                        value: '',
                        text: 'Select visit (optional)...'
                    });
                    visits.forEach(v => {
                        visitSelectize.addOption({
                            value: v.id,
                            text: `V-${v.visit_number} — ${v.visit_date}`
                        });
                    });
                    visitSelectize.refreshOptions(false);
                });
            }

            // ─── Item rows: Category → Product chain ────────────
            let itemIndex = {{ isset($invoice) ? $invoice->invoiceItems->count() : 1 }};

            $('#add-item-row').on('click', function() {
                $.get(`/invoices/item-row-template?idx=${itemIndex}`, function(html) {
                    $('#items-body').append(html);
                    itemIndex++;
                });
            });

            // Remove row
            $(document).on('click', '.remove-item-row', function() {
                if ($('#items-body tr').length === 1) return; // keep at least 1
                $(this).closest('tr').remove();
                recalculateTotal();
            });

            // On category change → load products
            $(document).on('change', '.category-select', function() {
                const row = $(this).closest('tr');
                const categoryId = $(this).val();
                const productSelect = row.find('.product-select');

                productSelect.html('<option value="">Loading...</option>');

                $.get(`/invoices/products/${categoryId}`, function(products) {
                    let options = '<option value="">Select product...</option>';
                    products.forEach(p => {
                        options +=
                            `<option value="${p.id}" data-mrp="${p.price}" data-rate="${p.price}">${p.name}</option>`;
                    });
                    productSelect.html(options);
                });
            });

            // On product change → fill MRP and Net Sale Price
            $(document).on('change', '.product-select', function() {
                const row = $(this).closest('tr');
                const selected = $(this).find(':selected');
                const mrp = parseFloat(selected.data('mrp')) || 0;
                const rate = parseFloat(selected.data('rate')) || 0;

                row.find('.mrp-input').val(mrp.toFixed(2));
                row.find('.rate-input').val(rate.toFixed(2));
                calculateRowTotal(row);
            });

            // On qty / discount / rate change → recalculate row
            $(document).on('input', '.qty-input, .discount-input, .rate-input', function() {
                calculateRowTotal($(this).closest('tr'));
            });

            function calculateRowTotal(row) {
                const qty = parseFloat(row.find('.qty-input').val()) || 0;
                const discount = parseFloat(row.find('.discount-input').val()) || 0;
                const mrp = parseFloat(row.find('.mrp-input').val()) || 0;

                // Net Sale Price = MRP - (MRP * discount / 100)
                const netPrice = mrp - (mrp * discount / 100);
                row.find('.rate-input').val(netPrice.toFixed(2));

                const total = qty * netPrice;
                row.find('.amount-input').val(total.toFixed(2));
                row.find('.amount-display').text('₹' + total.toFixed(2));

                recalculateTotal();
            }

            function recalculateTotal() {
                let total = 0;
                $('.amount-input').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#total-po-amount-display').val('₹' + total.toFixed(2));
                $('#total-po-amount').val(total.toFixed(2));
            }

            // ─── PDC rows ────────────────────────────────────────
            let pdcIndex = {{ isset($invoice) ? $invoice->pdcs->count() : 1 }};

            $('#add-pdc-row').on('click', function() {
                const html = pdcRowTemplate(pdcIndex);
                $('#pdc-body').append(html);
                pdcIndex++;
            });

            $(document).on('click', '.remove-pdc-row', function() {
                $(this).closest('.pdc-row').remove();
            });

            function pdcRowTemplate(idx) {
                return `
        <div class="pdc-row border rounded p-3 mb-2 d-flex align-items-center gap-3 flex-wrap">
            <span class="badge bg-success">PDC ${idx + 1}</span>
            <div>
                <label class="form-label mb-1 small">Date</label>
                <input type="date" name="pdc_dates[]" class="form-control form-control-sm" style="width:140px">
            </div>
            <div>
                <label class="form-label mb-1 small">Cheque Number</label>
                <input type="text" name="pdc_cheque_numbers[]" class="form-control form-control-sm" placeholder="Cheque no." style="width:160px">
            </div>
            <div>
                <label class="form-label mb-1 small">Bank Name</label>
                <input type="text" name="pdc_bank_names[]" class="form-control form-control-sm" placeholder="Bank name" style="width:160px">
            </div>
            <div>
                <label class="form-label mb-1 small">Amount (₹)</label>
                <input type="number" step="0.01" name="pdc_amounts[]" class="form-control form-control-sm" placeholder="0.00" style="width:120px">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-pdc-row ms-auto mt-3">
                <i class="feather icon-trash-2"></i>
            </button>
        </div>`;
            }

            // ─── Init on page load ───────────────────────────────
            recalculateTotal();

        });
    </script>
@endpush
