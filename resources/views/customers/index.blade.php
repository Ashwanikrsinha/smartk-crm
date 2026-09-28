@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="mb-0">Schools</h5>
            <small class="text-muted">All registered schools</small>
        </div>
        @can('create', App\Models\Customer::class)
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <a href="{{ route('customers.sample-export') }}" class="btn btn-outline-success btn-sm">
                    <i class="feather icon-download me-1"></i> Download Sample
                </a>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                    <i class="feather icon-upload me-1"></i> Bulk Upload
                </button>
                <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather icon-plus me-1"></i> New School
                </a>
            </div>
        @endcan
    </header>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="feather icon-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="feather icon-alert-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('import_errors') && count(session('import_errors')) > 0)
        <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
            <div class="fw-bold mb-1">
                <i class="feather icon-alert-circle me-1"></i> Import Issues ({{ count(session('import_errors')) }}):
            </div>
            <ul class="mb-0 ps-3" style="max-height: 180px; overflow-y: auto;">
                @foreach (session('import_errors') as $err)
                    <li><small>{{ $err }}</small></li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="bg-white rounded shadow-sm p-3">
        <x-datatable id="schools" :columns="['Code', 'School Name', 'City', 'State', 'Phone', 'Lead Source', 'SP', 'POs', 'Actions']" />
    </div>

    @can('create', App\Models\Customer::class)
        {{-- Bulk Upload Modal --}}
        <div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold" id="bulkUploadModalLabel">
                            <i class="feather icon-upload-cloud text-primary me-2"></i> Bulk School Data Upload
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('customers.bulk-upload') }}" method="POST" enctype="multipart/form-data" id="bulk-upload-form">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info py-2 px-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <i class="feather icon-info me-1"></i> Download the sample template and fill in the school details before uploading.
                                    </div>
                                    <a href="{{ route('customers.sample-export') }}" class="btn btn-sm btn-success text-white">
                                        <i class="feather icon-download me-1"></i> Download Sample Excel
                                    </a>
                                </div>
                            </div>

                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold mb-2 text-dark font-size-sm">Important Guidelines:</h6>
                                    <ul class="text-muted small mb-0 ps-3">
                                        <li><strong>Mandatory Fields:</strong> School Name, Mobile Number, State, City, Pin Code.</li>
                                        <li><strong>Optional Fields:</strong> Email ID, Address, GSTIN, Lead Source, Description, Contact Person, Contact Number, Designation, Employee Code.</li>
                                        <li><strong>Lead Source & Designation:</strong> You can enter either the exact <em>ID</em> or the <em>Name</em> (refer to the "Reference Guide" sheet in the sample file).</li>
                                        <li><strong>Employee Code:</strong> For Administrators, specifying an Employee Code (e.g. <code>EMP-2026-0001</code>) assigns the school to that team member. Otherwise, it defaults to the logged-in user.</li>
                                        <li><strong>Supported Formats:</strong> <code>.xlsx</code>, <code>.xls</code>, <code>.csv</code> (Max size: 10 MB).</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="upload_file" class="form-label fw-bold">Select Excel / CSV File <span class="text-danger">*</span></label>
                                <input type="file" name="file" id="upload_file" class="form-control" accept=".xlsx, .xls, .csv" required>
                                <div class="form-text">Choose a valid <code>.xlsx</code> or <code>.csv</code> file formatted according to the sample.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="btn-submit-upload">
                                <i class="feather icon-upload me-1"></i> Upload & Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('table#schools').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, 'desc']
                ],
                ajax: '{{ route('customers.index') }}',
                columns: [{
                        data: 'school_code',
                        name: 'school_code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'state',
                        name: 'state'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number',
                        searchable: false
                    },
                    {
                        data: 'lead_source',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_name_emp_code',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'po_count',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });
        });
    </script>
@endpush
