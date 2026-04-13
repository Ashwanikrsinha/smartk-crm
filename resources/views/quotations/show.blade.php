@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
    <h5>Quotation</h5>
    <a href="{{ route('quotations.index') }}" class="btn btn-sm btn-secondary">Back</a>
</header>

<div class="card shadow-sm rounded border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-calendar me-1 bg-primary text-white rounded p-1"></i>
        Quotation No. {{ $quotation->quotation_number }}
    </header>
    <div class="card-body p-0">

        <section class="table-responsive mb-4">
            <table class="table" style="min-width: 60rem;">
                <tbody>
                    <tr>
                        <th class="ps-3">Quotation Date</th>
                        <td>{{ $quotation->quotation_date->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Visit No.</th>
                        <td>
                            <a href="{{ route('visits.show', ['visit' => $quotation->visit]) }}" target="_blank">
                                V-{{ $quotation->visit->visit_number }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th class="ps-3">Customer Name</th>
                        <td>
                            {{ $quotation->customer->name }}
                        </td>
                    </tr>
                    <tr>
                        <th class="ps-3">Username</th>
                        <td>{{ $quotation->user->username }}</td>
                    </tr>
                    
                    <tr>
                        <th class="ps-3">Status</th>
                        <td>{{ ucfirst($quotation->status) }}</td>
                    </tr> 
                    
                    <tr>
                        <th class="ps-3">Follow Up Date</th>
                        <td>{{ isset($quotation->follow_date) ? $quotation->follow_date->format('d M, Y') : '' }}</td>
                    </tr>  
                    <tr>
                        <th class="ps-3">Follow Up Type</th>
                        <td>{{ ucfirst($quotation->follow_type) }}</td>
                    </tr> 
                    
                    <tr>
                        <th class="ps-3">Reason</th>
                        <td style="max-width: 20rem;">{{ $quotation->reason }}</td>
                    </tr> 
                </tbody>
            </table>
        </section>
    </div>
</div>


<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-book me-1 bg-primary text-white rounded p-1"></i> Remarks
    </header>
    <div class="card-body table-responsive" id="description">
        {{ $quotation->remarks ?? 'NOT GIVEN' }}
    </div>
</div>



{{-- items--}}
@if($quotation->quotationItems->count() > 0)

<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-file me-1 bg-primary text-white rounded p-1"></i>
        Quotation Items
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
                    @foreach($quotation->quotationItems as $quotationItem)
                    <tr>
                        <td class="ps-3">{{ $quotationItem->product->name }}</td>
                        <td>{{ $quotationItem->description }}</td>
                        <td>{{ $quotationItem->quantity }}</td>
                        <td>{{ $quotationItem->unit->name }}</td>
                        <td>{{ $quotationItem->rate }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>
    @else
    <div class="alert alert-primary shadow-sm border-0 mb-4">No Quotation Items</div>
    @endif



    @if($quotation->attachments->count() > 0)
    <div class="card shadow-sm border-0 mb-4">
        <header class="card-header bg-white py-3">
            <i class="feather icon-file me-1 bg-primary text-white rounded p-1"></i>
            Attachments
        </header>
        <div class="card-body">
            @foreach ($quotation->attachments as $attachment)
            <a href="{{ url('storage/'.$attachment->filename) }}" target="_blank" class="d-block mb-3">
                <i class="feather icon-file me-1 fs-5"></i>
                <span class="text-muted">{{ $attachment->filename }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif


    @endsection