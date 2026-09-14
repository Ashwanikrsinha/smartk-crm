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

{{-- ═══ USER FILTER ═════════════════════════════════════════ --}}
<div class="bg-white rounded shadow-sm p-3 mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-lg-4 col-md-6">
            <label class="form-label small mb-1">Filter by User</label>
            <select id="user-filter" class="form-control form-control-sm">
                <option value="">All Users (Holistic View)</option>
                @foreach ($teamUsers as $u)
                    <option value="{{ $u->id }}">
                        {{ $u->username }} ({{ $u->emp_code ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary btn-sm w-100">
                <i class="feather icon-rotate-ccw me-1"></i> Reset
            </button>
        </div>
    </div>
</div>

<div class="bg-white rounded shadow-sm p-3">
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle w-100" id="targets-table">
            <thead class="table-light">
                <tr>
                    <th>SP Name</th>
                    <th>Period</th>
                    <th class="text-end">Target Amount</th>
                    <th class="text-end">Achieved (Net Billed)</th>
                    <th style="min-width:140px">Achievement %</th>
                    <th class="text-center" style="width:90px">Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot class="table-dark fw-bold">
                <tr>
                    <td colspan="2">Total</td>
                    <td class="text-end" id="footer-target">₹0.00</td>
                    <td class="text-end" id="footer-achieved">₹0.00</td>
                    <td id="footer-pct">—</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    const table = $('#targets-table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        ajax: {
            url: '{{ route('targets.index') }}',
            data: function (d) {
                d.user_id = $('#user-filter').val();
            }
        },
        columns: [
            { data: 'user_name_emp_code', name: 'user_name_emp_code' },
            { data: 'month',             name: 'month' },
            { data: 'target_amount',     name: 'target_amount', className: 'text-end' },
            { data: 'achieved',          name: 'achieved', className: 'text-end' },
            { data: 'achievement_pct',   name: 'achievement_pct' },
            { data: 'action',            name: 'action', className: 'text-center' },
        ],
        drawCallback: function (settings) {
            const json = settings.json;
            if (json) {
                $('#footer-target').text(json.total_target || '₹0.00');
                $('#footer-achieved').text(json.total_achieved || '₹0.00');
                $('#footer-pct').html(json.overall_pct_html || '—');
            }
        }
    });
    $('#user-filter').selectize();
    $('#user-filter').on('change', function () {
        table.ajax.reload();
    });

    $('#btn-reset-filter').on('click', function () {
        $('#user-filter').val('');
        table.ajax.reload();
    });
});
</script>
@endpush
