@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
    <h5>Visit</h5>
    <a href="{{ route('visits.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-calendar me-1 bg-primary text-white rounded p-1"></i> 
        Visit No. {{ $visit->visit_number }}
    </header>
    <div class="card-body p-0">
        <section class="table-responsive mb-4">
            <table class="table" style="min-width: 60rem;">
                <tbody>
                    <tr>
                        <th class="ps-3">Visit Date</th>
                        <td>{{ $visit->visit_date->format('d M, Y') }}</td>
                    </tr> 
                    <tr>
                        <th class="ps-3">Customer Name</th>
                        <td>
                            {{ $visit->customer->name  }}
                        </td>
                    </tr> 
                    <tr>
                        <th class="ps-3">Segment</th>
                        <td>{{ $visit->customer->segment->name }}</td>
                    </tr> 
                    <tr>
                        <th class="ps-3">Username</th>
                        <td>{{ $visit->user->username }}</td>
                    </tr> 
                    <tr>
                        <th class="ps-3">Purpose</th>
                        <td>{{ $visit->purpose->name }}</td>
                    </tr> 
                    
                    <tr>
                        <th class="ps-3">Level</th>
                        <td>{{ ucfirst($visit->level) }}</td>
                    </tr> 
                    <tr>
                        <th class="ps-3">Status</th>
                        <td>{{ ucfirst($visit->status) }}</td>
                    </tr> 
                    
                    <tr>
                        <th class="ps-3">Follow Up Date</th>
                        <td>{{ isset($visit->follow_date) ? $visit->follow_date->format('d M, Y') : '' }}</td>
                    </tr>  
                    <tr>
                        <th class="ps-3">Follow Up Type</th>
                        <td>{{ ucfirst($visit->follow_type) }}</td>
                    </tr> 
                    <tr>
                        <th class="ps-3">Order Amount</th>
                        <td>{{ $visit->order_amount }}</td>
                    </tr> 
                    <tr>
                        <th class="ps-3">Insight</th>
                        <td style="max-width: 20rem;">{{ $visit->insight }}</td>
                    </tr> 
                    <tr>
                        <th class="ps-3">Action</th>
                        <td style="max-width: 20rem;">{{ $visit->action }}</td>
                    </tr> 
                    <tr>
                        <th class="ps-3">Reason</th>
                        <td style="max-width: 20rem;">{{ $visit->reason }}</td>
                    </tr> 
                </tbody>
            </table>
        </section>
    </div>
</div>    


<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-book me-1 bg-primary text-white rounded p-1"></i> Description
    </header>
    <div class="card-body table-responsive" id="description">
        {!! $visit->description ?? 'NOT GIVEN' !!}
    </div>
</div>

{{-- items--}}
@if($visit->visitItems->count() > 0)

<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-file me-1 bg-primary text-white rounded p-1"></i> 
        Visit Items
    </header>
    <div class="card-body p-0">
        <section class="table-responsive rounded mb-2">
            <table class="table" style="min-width: 60rem;">
                <thead>
                    <tr>
                        <th class="ps-3">Product Name</th>
                        <th>Description</th>
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($visit->visitItems as $visitItem)
                    <tr>
                        <td class="ps-3">{{ $visitItem->product->name }}</td>
                        <td>{{ $visitItem->description }}</td>
                        <td>{{ $visitItem->quantity }}</td>
                        <td>{{ $visitItem->unit->name }}</td>
                        <td>{{ $visitItem->rate }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>
@else
<div class="alert alert-primary shadow-sm border-0 mb-4">No Visit Items</div>
@endif



@if($visit->attachments->count() > 0)
<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-file me-1 bg-primary text-white rounded p-1"></i> 
        Attachments
    </header>
    <div class="card-body">
        @foreach ($visit->attachments as $attachment)
            <a href="{{ url('storage/'.$attachment->filename) }}" target="_blank" class="d-block mb-3">
            <i class="feather icon-file me-1 fs-5"></i> 
            <span class="text-muted">{{ $attachment->filename }}</span>
            </a>
        @endforeach   
    </div>
</div>
@endif

@endsection


@push('scripts')

<script>
    $(document).ready(() => {
      
      $('#description table').each(function(){ $(this).addClass('table table-bordered w-100')});

    });
</script>

@endpush