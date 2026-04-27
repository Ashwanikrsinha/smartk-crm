@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Schools</h5>
            <small class="text-muted">All registered schools</small>
        </div>
        @can('create', App\Models\Customer::class)
            <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                <i class="feather icon-plus me-1"></i> New School
            </a>
        @endcan
    </header>

    <div class="bg-white rounded shadow-sm p-3">
        <x-datatable id="schools" :columns="['Code', 'School Name', 'City', 'State', 'Phone', 'Lead Source', 'SP', 'POs', 'Actions']" />
    </div>
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
                        data: 'created_by',
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
