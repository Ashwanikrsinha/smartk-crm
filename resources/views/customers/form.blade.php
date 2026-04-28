@csrf

{{-- ═══ SCHOOL INFORMATION ════════════════════════════════ --}}
<h6 class="fw-bold border-bottom pb-2 mb-3">
    <i class="feather icon-home me-2 text-primary"></i> School Information
</h6>

<div class="row">

    <div class="col-lg-6 mb-3">
        <label class="form-label">School Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ $customer->name ?? old('name') }}"
            placeholder="Full school name" required>
    </div>

    <div class="col-lg-6 mb-3">
        <label class="form-label">Lead From</label>
        <select name="lead_source_id" class="form-control">
            <option value="">Select source...</option>
            @foreach ($lead_sources as $id => $name)
                <option value="{{ $id }}"
                    {{ (isset($customer) && $customer->lead_source_id == $id) || old('lead_source_id') == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- <div class="col-lg-3 mb-3">
        <label class="form-label">Segment</label>
        <select name="segment_id" class="form-control">
            <option value="">Select segment...</option>
            @foreach ($segments as $id => $name)
                <option value="{{ $id }}"
                    {{ (isset($customer) && $customer->segment_id == $id) || old('segment_id') == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div> --}}

    <div class="col-lg-4 mb-3">
        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
        <input type="text" name="phone_number" class="form-control"
            value="{{ $customer->phone_number ?? old('phone_number') }}" placeholder="10-digit mobile" maxlength="15"
            required>
    </div>

    <div class="col-lg-4 mb-3">
        <label class="form-label">Email ID</label>
        <input type="email" name="email" class="form-control" value="{{ $customer->email ?? old('email') }}"
            placeholder="school@example.com">
    </div>

    <div class="col-lg-4 mb-3">
        <label class="form-label">GSTIN</label>
        <input type="text" name="gstin" class="form-control" value="{{ $customer->gstin ?? old('gstin') }}"
            placeholder="15-character GST number" maxlength="15"
            pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$"
            title="Must be a valid 15-character GSTIN">
        <div class="form-text">Format: 27AAPFU0939F1ZV</div>
    </div>

</div>

{{-- ═══ ADDRESS ════════════════════════════════════════════ --}}
<h6 class="fw-bold border-bottom pb-2 mb-3 mt-2">
    <i class="feather icon-map-pin me-2 text-primary"></i> Address Details
</h6>

<div class="row">

    <div class="col-lg-6 mb-3">
        <label class="form-label">School Address</label>
        <input type="text" name="address" class="form-control" value="{{ $customer->address ?? old('address') }}"
            placeholder="Street / Area">
    </div>

    <div class="col-lg-3 mb-3">
        <label class="form-label">State <span class="text-danger">*</span></label>
        <input type="text" name="state" class="form-control" value="{{ $customer->state ?? old('state') }}"
            list="state-datalist" placeholder="State" required>
        <datalist id="state-datalist">
            @foreach ($states as $st)
                <option value="{{ $st }}">
            @endforeach
        </datalist>
    </div>

    <div class="col-lg-2 mb-3">
        <label class="form-label">City <span class="text-danger">*</span></label>
        <input type="text" name="city" class="form-control" value="{{ $customer->city ?? old('city') }}"
            placeholder="City" required>
    </div>

    <div class="col-lg-1 mb-3">
        <label class="form-label">Pin Code</label>
        <input type="text" name="pin_code" class="form-control" value="{{ $customer->pin_code ?? old('pin_code') }}"
            placeholder="6-digit" maxlength="6">
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">Description / Notes</label>
        <textarea name="description" class="form-control" rows="2"
            placeholder="Any additional notes about this school...">{{ $customer->description ?? old('description') }}</textarea>
    </div>

</div>


{{-- ═══ CONTACT PERSONS ════════════════════════════════════ --}}
<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-2">
    <h6 class="fw-bold mb-0">
        <i class="feather icon-users me-2 text-primary"></i> Contact Persons
    </h6>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-primary" id="add-contact-row">
            <i class="feather icon-plus"></i> Add
        </button>
        <button type="button" class="btn btn-sm btn-danger" id="remove-contact-row">
            <i class="feather icon-x"></i> Remove
        </button>
    </div>
</div>

<div class="table-responsive rounded mb-4">
    <table class="table table-bordered" id="contact-table" style="min-width: 50rem;">
        <thead class="table-light">
            <tr>
                <th>Person Name</th>
                <th>Birth Date</th>
                <th>Anniversary Date</th>
                <th>Contact No.</th>
                <th>Designation</th>
            </tr>
        </thead>
        <tbody id="contact-tbody">

            @if (isset($customer) && $customer->contacts->count() > 0)

                @foreach ($customer->contacts as $contact)
                    <tr>
                        <td>
                            <input type="text" name="person_name[]" value="{{ $contact->name }}"
                                class="form-control">
                        </td>
                        <td>
                            <input type="date" name="birth_date[]"
                                value="{{ $contact->birth_date ? $contact->birth_date->format('Y-m-d') : '' }}"
                                class="form-control">
                        </td>
                        <td>
                            <input type="date" name="marriage_date[]"
                                value="{{ $contact->marriage_date ? $contact->marriage_date->format('Y-m-d') : '' }}"
                                class="form-control">
                        </td>
                        <td>
                            <input type="text" name="contact_number[]" value="{{ $contact->contact_number }}"
                                class="form-control">
                        </td>
                        <td style="min-width: 12rem;">
                            <select name="designation_id[]" class="form-control">
                                <option value="">Choose...</option>
                                @foreach ($designations as $id => $designation)
                                    <option value="{{ $id }}"
                                        {{ $contact->designation_id == $id ? 'selected' : '' }}>
                                        {{ $designation }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @endforeach
            @else
                {{-- Default blank row --}}
                <tr>
                    <td><input type="text" name="person_name[]" class="form-control" placeholder="Contact name">
                    </td>
                    <td><input type="date" name="birth_date[]" class="form-control"></td>
                    <td><input type="date" name="marriage_date[]" class="form-control"></td>
                    <td><input type="text" name="contact_number[]" class="form-control"
                            placeholder="Phone number"></td>
                    <td style="min-width: 12rem;">
                        <select name="designation_id[]" class="form-control">
                            <option value="">Choose...</option>
                            @foreach ($designations as $id => $designation)
                                <option value="{{ $id }}">{{ $designation }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>

            @endif

        </tbody>
    </table>
</div>


{{-- ═══ DOCUMENTS (create mode only — edit via show page) ══ --}}
@if ($mode === 'create')
    <h6 class="fw-bold border-bottom pb-2 mb-3 mt-2">
        <i class="feather icon-file me-2 text-primary"></i> Documents
        <small class="text-muted fw-normal">(optional — can be uploaded later)</small>
    </h6>
    <div class="row">
        <div class="col-lg-4 mb-3">
            <label class="form-label">Aadhar Card</label>
            <input type="file" name="aadhar" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            <div class="form-text">PDF or image, max 5MB</div>
        </div>
        <div class="col-lg-4 mb-3">
            <label class="form-label">PAN Card</label>
            <input type="file" name="pan" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        </div>
        <div class="col-lg-4 mb-3">
            <label class="form-label">GST Certificate</label>
            <input type="file" name="gst_certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        </div>
    </div>
@endif


{{-- ═══ ACTIONS ════════════════════════════════════════════ --}}
<div class="d-flex gap-2 mt-3 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <i class="feather icon-save me-1"></i>
        {{ $mode === 'create' ? 'Register School' : 'Update School' }}
    </button>
    <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
</div>


@push('scripts')
    <script>
        $(document).ready(function() {

            $('select').selectize();

            // ── Contact row template ─────────────────────────────
            // Build designation options from existing select (avoids PHP in JS)
            const designationOptions =
                `@foreach ($designations as $id => $designation)<option value="{{ $id }}">{{ $designation }}</option>@endforeach`;

            const newRow = () => `
        <tr>
            <td><input type="text"  name="person_name[]"    class="form-control" placeholder="Contact name"></td>
            <td><input type="date"  name="birth_date[]"     class="form-control"></td>
            <td><input type="date"  name="marriage_date[]"  class="form-control"></td>
            <td><input type="text"  name="contact_number[]" class="form-control" placeholder="Phone number"></td>
            <td style="min-width: 12rem;">
                <select name="designation_id[]" class="form-control">
                    <option value="">Choose...</option>
                    ${designationOptions}
                </select>
            </td>
        </tr>`;

            // Add row
            $('#add-contact-row').on('click', function() {
                $('#contact-tbody').append(newRow());
            });

            // Remove last row (keep at least one)
            $('#remove-contact-row').on('click', function() {
                const rows = $('#contact-tbody tr');
                if (rows.length > 1) {
                    rows.last().remove();
                }
            });

        });
    </script>
@endpush
