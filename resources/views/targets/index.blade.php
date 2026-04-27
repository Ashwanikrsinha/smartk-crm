@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0">Targets</h5>
        <small class="text-muted">Monthly sales targets per Sales Person</small>
    </div>
    @if(auth()->user()->isSalesManager() || auth()->user()->isAdmin())
    <a href="{{ route('targets.create') }}" class="btn btn-primary btn-sm">
        <i class="feather icon-plus me-1"></i> Set Target
    </a>
    @endif
</header>

<div class="bg-white rounded shadow-sm p-3">
    <x-datatable id="targets"
        :columns="['SP Name', 'Period', 'Target Amount', 'Achieved', 'Achievement %', 'Actions']" />
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('table#targets').DataTable({
        processing: true,
        serverSide: true,
        order:      [[1, 'desc']],
        ajax:       '{{ route('targets.index') }}',
        columns: [
            { data: 'user.username',     name: 'user.username' },
            { data: 'month',             name: 'month', searchable: false },
            { data: 'target_amount',     name: 'target_amount', searchable: false },
            { data: 'achieved',          orderable: false, searchable: false },
            { data: 'achievement_pct',   orderable: false, searchable: false },
            { data: 'action',            orderable: false, searchable: false },
        ],
    });
});
</script>
@endpush
