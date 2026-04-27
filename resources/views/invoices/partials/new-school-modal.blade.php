<div class="modal fade" id="new-school-modal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="feather icon-home me-2 text-primary"></i>
                    Register New School
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div id="school-modal-errors" class="alert alert-danger d-none"></div>

                <div class="row">

                    {{-- Lead From --}}
                    <div class="col-lg-4 mb-3">
                        <label class="form-label">Lead From <span class="text-danger">*</span></label>
                        <select name="_lead_source_id" id="modal-lead-source" class="form-control" required>
                            <option value="">Select source...</option>
                            @foreach ($lead_sources as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- School Name --}}
                    <div class="col-lg-4 mb-3">
                        <label class="form-label">School Name <span class="text-danger">*</span></label>
                        <input type="text" id="modal-school-name" class="form-control" placeholder="Full school name"
                            required>
                    </div>

                    {{-- Contact Person --}}
                    <div class="col-lg-4 mb-3">
                        <label class="form-label">Contact Person Name</label>
                        <input type="text" id="modal-contact-person" class="form-control"
                            placeholder="Principal / Coordinator">
                    </div>

                    {{-- Mobile --}}
                    <div class="col-lg-4 mb-3">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" id="modal-mobile" class="form-control" placeholder="10-digit mobile"
                            maxlength="10">
                    </div>

                    {{-- Email --}}
                    <div class="col-lg-4 mb-3">
                        <label class="form-label">Email ID</label>
                        <input type="email" id="modal-email" class="form-control" placeholder="school@example.com">
                    </div>

                    {{-- Address --}}
                    <div class="col-lg-8 mb-3">
                        <label class="form-label">School Address</label>
                        <input type="text" id="modal-address" class="form-control" placeholder="Full address">
                    </div>

                    {{-- State --}}
                    <div class="col-lg-3 mb-3">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <input type="text" id="modal-state" class="form-control" list="states-list"
                            placeholder="State">
                        <datalist id="states-list">
                            @foreach ($states as $state)
                                <option value="{{ $state }}">
                            @endforeach
                        </datalist>
                    </div>

                    {{-- City --}}
                    <div class="col-lg-3 mb-3">
                        <label class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" id="modal-city" class="form-control" placeholder="City">
                    </div>

                    {{-- Pin Code --}}
                    <div class="col-lg-2 mb-3">
                        <label class="form-label">Pin Code</label>
                        <input type="text" id="modal-pin" class="form-control" placeholder="6-digit" maxlength="6">
                    </div>

                    <hr class="my-2">
                    <h6 class="mb-3 text-muted small text-uppercase">Documents</h6>

                    {{-- Aadhar Upload --}}
                    <div class="col-lg-4 mb-3">
                        <label class="form-label">Aadhar Card Upload</label>
                        <input type="file" id="modal-aadhar" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>

                    {{-- PAN Upload --}}
                    <div class="col-lg-4 mb-3">
                        <label class="form-label">PAN Card Upload</label>
                        <input type="file" id="modal-pan" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>

                    {{-- GST Certificate --}}
                    <div class="col-lg-4 mb-3">
                        <label class="form-label">School GST Certificate</label>
                        <input type="file" id="modal-gst-cert" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-new-school">
                    <span id="school-save-spinner" class="spinner-border spinner-border-sm d-none me-1"></span>
                    Register School
                </button>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script>
        $('#save-new-school').on('click', function() {

            const btn = $(this);
            const spinner = $('#school-save-spinner');
            const errors = $('#school-modal-errors');

            // Basic validation
            const name = $('#modal-school-name').val().trim();
            const mobile = $('#modal-mobile').val().trim();
            const state = $('#modal-state').val().trim();
            const city = $('#modal-city').val().trim();

            if (!name || !mobile || !state || !city) {
                errors.removeClass('d-none').text('School Name, Mobile, State and City are required.');
                return;
            }

            errors.addClass('d-none');
            btn.prop('disabled', true);
            spinner.removeClass('d-none');

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('name', name);
            formData.append('lead_source_id', $('#modal-lead-source').val());
            formData.append('contact_person', $('#modal-contact-person').val());
            formData.append('phone_number', mobile);
            formData.append('email', $('#modal-email').val());
            formData.append('address', $('#modal-address').val());
            formData.append('state', state);
            formData.append('city', city);
            formData.append('pin_code', $('#modal-pin').val());

            if ($('#modal-aadhar')[0].files[0]) formData.append('aadhar', $('#modal-aadhar')[0].files[0]);
            if ($('#modal-pan')[0].files[0]) formData.append('pan', $('#modal-pan')[0].files[0]);
            if ($('#modal-gst-cert')[0].files[0]) formData.append('gst_certificate', $('#modal-gst-cert')[0].files[
                0]);

            $.ajax({
                url: '{{ route('customers.store') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Add new school to the main select and choose it
                    const schoolSelect = $('#school-select')[0].selectize;
                    schoolSelect.addOption({
                        value: response.id,
                        text: `[${response.school_code}] ${response.name} — ${response.city}, ${response.state}`
                    });
                    schoolSelect.setValue(response.id);

                    // Close modal and reset
                    bootstrap.Modal.getInstance(document.getElementById('new-school-modal')).hide();
                    $('#new-school-modal input, #new-school-modal select').val('');
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message ??
                        'Failed to register school. Please try again.';
                    errors.removeClass('d-none').text(msg);
                },
                complete: function() {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });

        });
    </script>
@endpush
