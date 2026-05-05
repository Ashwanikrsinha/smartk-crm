@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Post Dated Cheques</h5>
            <small class="text-muted">Track and update cheque clearance status</small>
        </div>
    </header>

    <div class="bg-white rounded shadow-sm p-3">
        <x-datatable id="pdcs" :columns="['PO Number', 'School', 'SP', 'PDC', 'Cheque No.', 'Bank', 'Date', 'Amount', 'Status', 'Actions']" />
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('table#pdcs').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [6, 'asc']
                ],
                ajax: '{{ route('pdcs.index') }}',
                columns: [{
                        data: 'po_number',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'school',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_name_emp_code',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'pdc_label',
                        name: 'pdc_label'
                    },
                    {
                        data: 'cheque_number',
                        name: 'cheque_number'
                    },
                    {
                        data: 'bank_name',
                        name: 'bank_name',
                        searchable: false
                    },
                    {
                        data: 'cheque_date',
                        name: 'cheque_date'
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
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
