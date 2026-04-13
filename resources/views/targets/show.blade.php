@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5 class="d-inline-block me-2 mb-0">Target</h5>
    <a href="{{ route('targets.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<div class="card border-0 shadow-sm mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-target me-1 bg-primary text-white rounded p-1"></i>
        Target
    </header>
  <div class="card-body p-0">
    <section class="table-responsive mb-4">
        <table class="table align-middle" style="min-width: 40rem;">
            <tbody>
                <tr>
                    <th class="ps-3">Target</th>
                    <td style="max-width: 20rem;">{{ $target->target }}/{{ $total_visits }}</td>
                </tr> 
                <tr>
                    <th class="ps-3">Execeitive Name</th>
                    <td>
                        {{ $target->user->username }}
                    </td>
                </tr>
                <tr>
                    <th class="ps-3">Start Date</th>
                    <td>{{$target->start_date->format('d M, Y') }}</td>
                </tr> 
                <tr>
                    <th class="ps-3">End Date</th>
                    <td>{{$target->end_date->format('d M, Y') }}</td>
                </tr>  
                <tr>
                    <th class="ps-3">Complete</th>
                    <td>{{ $total_visits * 100 / $target->target }}%</td>
                </tr> 
                
            </tbody>
        </table>
    </section>
  </div>
</div>


@if($visits->count() > 0)
<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-calendar me-1 bg-primary text-white rounded p-1"></i> Visits
    </header>
    <div class="card-body p-0">
        <section class="table-responsive rounded">
            <table class="table" id="visits" style="min-width: 50rem;">
                <thead>
                    <tr>
                        <th class="ps-3">Visit Date</th>
                        <th>Executive</th>
                        <th>Product</th>
                        <th>Purpose</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($visits as $visit)
                    <tr>
                        <td class="ps-3">{{ $visit->visit_date->format('d M, Y') }}</td>
                        <td>{{ $visit->user->username }}</td>
                        <td>{{ $visit->product->name }}</td>
                        <td>{{ $visit->purpose->name }}</td>
                        <td>
                            <button type="button" class="btn btn-sm" data-bs-toggle="tooltip"
                             data-bs-placement="top"
                             data-bs-html="true"
                             title="{{ $visit->description ?? 'NOT GIVEN' }}">
                                <i class="feather icon-info text-primary"></i>
                              </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $visits->links() }}
        </section>
    </div>
</div>
@endisset
 
@endsection

@push('scripts')
    
<script>

   $(document).ready(()=>{

        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
        });

   });

</script>

@endpush