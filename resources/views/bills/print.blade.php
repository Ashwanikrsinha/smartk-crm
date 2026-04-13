@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center d-print-none">
    <h5>
        {{ ucwords($bill->type) }} Bill - {{ $bill->bill_number }} 
        ({{ $type == App\Models\Bill::WITH_PRICE ? 'With Price' : 'Without Price' }})
    </h5>
    <div>
        <button onclick="return window.print();" class="btn btn-sm btn-success text-white">Print</button>
        <a href="{{ route('bills.index', ['type' => $bill->type]) }}"  class="btn btn-sm btn-secondary">Back</a>
    </div>
</header>

<section class="shadow-sm rounded bg-white p-4" id="bill">

 <div class="row mb-4">
     <div class="col-6">
        <strong class="small">GST IN : {{ $company->gst_number }}</strong>
     </div>
     <div class="col-6 text-end">
        <img src="{{ url('storage/'.$company->logo) }}"
        alt="{{ url('storage/'.$company->logo) }}" style="max-width: 140px;">
     </div>
 </div>

 <div class="row mb-4">
    <div class="col-6">
        <h5 class="mb-3 fw-bold">{{ ucwords($bill->type) }} Bill</h5>
        <table class="table table-bordered small w-75">
            <tbody>
                <tr>
                    <td>BILL NO.</td>
                    <td>{{ $bill->bill_number }}</td>
                </tr>
                <tr>
                    <td>DATE</td>
                    <td>{{ $bill->bill_date->format('d M, Y') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-6 text-end">
        <div class="w-75 ms-auto small">
            <p class="mb-1">{{ $company->address }}</p>
            <p class="mb-1"><b class="me-1">Phone No :</b> {{ $company->phone_number }}</p>
            <p><b class="me-1">Email :</b> {{ $company->email }}</p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-6">
        <table class="table border small">
            <thead class="bg-light">
                <tr>
                    <td>
                        <strong>CUSTOMER INFO.</strong>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <p class="mb-1"><b class="me-1">Name :</b>{{ $bill->customer->name }}</p>
                        <p class="mb-1"><b class="me-1">Name :</b>{{ $bill->gst_number }}</p>
                        <p class="mb-1"><b class="me-1">Phone No :</b> {{ $bill->phone_number }}</p>
                        <p><b class="me-1">Address :</b> {{ $bill->address }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-6">
        <table class="table border small">
            <thead class="bg-light">
                <tr>
                    <td>
                        <strong>TRANSPORT INFO.</strong>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <p class="mb-1"><b class="me-1">Name :</b> {{ isset($bill->transport_id) ? $bill->transport->name : 'NOT GIVEN' }}</p>
                        <p class="mb-1"><b class="me-1">Phone No :</b> {{ isset($bill->transport_id) ? $bill->transport->phone_number : 'NOT GIVEN' }}</p>
                        
                        <p class="mb-1"><b class="me-1">Vehicle No :</b> {{ isset($bill->vehicle_number) ? $bill->vehicle_number : 'NOT GIVEN' }}</p>
                        <p class="mb-1"><b class="me-1">Bilty No :</b> {{ isset($bill->bilty_number) ? $bill->bilty_number : 'NOT GIVEN' }}</p>
                        <p class="mb-1"><b class="me-1">Delivery Date :</b> {{ isset($bill->delivery_date) ? $bill->delivery_date->format('d M, Y') : 'NOT GIVEN' }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<table class="table table-bordered small mb-4">
    <thead class="bg-light">
        <tr>
            <th colspan="7" class="text-center">Invoice Details</th>
        </tr>
        <tr>
            <th>Sr. No.</th>
            <th>Product Name</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Unit</th>
            @if ($type == App\Models\Bill::WITH_PRICE)
              <th>Amount</th>
            @endif
        </tr>
    </thead>
    <tbody>

        @foreach ($bill->billItems as $billItem)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $billItem->product->name }}</td>
            <td>{{ $billItem->description }}</td>
            <td>{{ $billItem->quantity }}</td>
            <td>{{ $billItem->rate }}</td>
            <td>{{ $billItem->unit->name }}</td>
            @if ($type == App\Models\Bill::WITH_PRICE)
              <td>{{ $billItem->amount }}</td>
            @endif 
        </tr>
        @endforeach
    </tbody>
     
    @if ($type == App\Models\Bill::WITH_PRICE)
        <tr class="fw-bold">
            <td colspan="3" class="text-end">Total :</td>
            <td>{{ $billItem->sum('quantity') }}</td>
            <td colspan="2"></td>
            <td>{{ $bill->amount }}</td>
        </tr>
    @endif

</table>



<div class="row mb-4">
    <div class="col-7">
        <table class="table small border" id="terms">
            <thead class="bg-light">
                <tr>
                    <td>
                        <strong>Terms &amp; Condition's </strong>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="p-2">{!! $bill->terms !!}</td>
                </tr>
            </tbody>
        </table>

    </div>
    <div class="col-5">
        @if ($type == App\Models\Bill::WITH_PRICE)
            <table class="table table-bordered small border">
                <thead>
                    <tr class="bg-light fw-bold text-center">
                        <th colspan="2">Sale Order Taxation Details</th>
                    </tr>

                </thead>
                <tbody>
                    <tr>
                        <td><b class="me-1">CGST Tax Amount</b> </td>
                        <td>{{ $bill->cgst_amount }}</td>
                    </tr>
                    <tr>
                        <td><b class="me-1">SGST Tax Amount</b> </td>
                        <td>{{ $bill->sgst_amount }}</td>
                    </tr>
                    <tr>
                        <td><b class="me-1">IGST Tax Amount</b> </td>
                        <td>{{ $bill->igst_amount }}</td>
                    </tr>

                    <tr>
                        <td><b class="me-1">Transport Charges</b> </td>
                        <td>{{ $bill->transport_charges }}</td>
                    </tr>
                    <tr>
                        <td><b class="me-1">Extra Charges</b> </td>
                        <td>{{ $bill->extra_charges }}</td>
                    </tr>
                    <tr>
                        <td><b class="me-1"> Amount</b> </td>
                        <td>{{ $bill->total_amount }}</td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>
</div>

<div class="text-end mb-5">
    @if ($type == App\Models\Bill::WITH_PRICE)
      <small><b class="me-1">Amount In Words : </b>{{ $amount_in_words }} RUPEES Rs Only /-</small>
    @endif
</div>


<div class="text-end mb-4">
    
    <small>
        <b class="me-1">For. {{ $company->name }}</b>
   </small>

</div>


<div class="row mb-4">
    <div class="col-6">
      <small class="text-muted">
          Authorised Signatory &amp; Stamp.
      </small>
    </div>
    <div class="col-6 text-end">
      <small class="text-muted">
        Authorised Signatory.
      </small>
    </div>
</div>

<hr>
<div class="mb-4 text-center">
    <small class="text-muted">{{ $bill->address }}</small>
</div>

</section>

@endsection

