@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">Lead Sources</h5>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('lead-sources.create') }}" class="btn btn-primary btn-sm">
                <i class="feather icon-plus me-1"></i> New Lead Source
            </a>
        @endif
    </header>

    <div class="bg-white rounded shadow-sm p-3">
        <x-datatable id="lead-sources" :columns="['#', 'Name', 'Schools Linked', 'Actions']" />
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('table#lead-sources').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, 'asc']
                ],
                ajax: '{{ route('lead-sources.index') }}',
                columns: [{
                        data: 'id',
                        name: 'id',
                        width: '5%'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'customers_count',
                        name: 'customers_count',
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
