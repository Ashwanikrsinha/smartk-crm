@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center d-print-none">
    <h5>
        Performa Invoice - {{ $invoice->invoice_number }} 
    </h5>
    <div>
        <button onclick="return window.print();" class="btn btn-sm btn-success text-white">Print</button>
        <a href="{{ route('invoices.index') }}"  class="btn btn-sm btn-secondary">Back</a>
    </div>
</header>

<section class="shadow-sm rounded bg-white p-4" id="invoice">

 <div class="row mb-4">
     <div class="col-6">
     </div>
     <div class="col-6 text-end">
        <img src="{{ url('storage/'.$company->logo) }}"
        alt="{{ url('storage/'.$company->logo) }}" style="max-width: 140px;">
     </div>
 </div>

 <div class="row mb-4">
    <div class="col-6">
        <h5 class="mb-3 fw-bold">Performa Invoice</h5>
        <table class="table table-bordered small w-75">
            <tbody>
                <tr>
                    <td>PERFORMA INVOICE. NO.</td>
                    <td>{{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td>DATE</td>
                    <td>{{ $invoice->invoice_date->format('d M, Y') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-6 text-end">
        <div class="w-75 ms-auto small">
            <p class="mb-1">{{ $company->address }}</p>
            <p class="mb-1"><b>Phone No :</b> {{ $company->phone_number }}</p>
            <p><b>Email :</b> {{ $company->email }}</p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-5">
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
                        <p class="mb-1"><b>Name :</b> {{ $invoice->customer->name }}</p>
                        <p class="mb-1"><b>Phone No :</b> {{ $invoice->phone_number }}</p>
                        <p><b>Address :</b> {{ $invoice->address }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-7"></div>
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
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($invoice->invoiceItems as $invoiceItem)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $invoiceItem->product->name }}</td>
            <td>{{ $invoiceItem->description }}</td>
            <td>{{ $invoiceItem->quantity }}</td>
            <td>{{ $invoiceItem->rate }}</td>
            <td>{{ $invoiceItem->unit->name }}</td>
            <td>{{ $invoiceItem->amount }}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr class="fw-bold">
            <td colspan="5"></td>
            <td>Total :</td>
            <td>{{ $invoice->amount }}</td>
        </tr>    
    </tfoot>
     

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
                    <td class="p-2">{!! $invoice->terms !!}</td>
                </tr>
            </tbody>
        </table>

    </div>
    <div class="col-5">
    </div>
</div>

<div class="text-end mb-5">
      <small><b>Amount In Words : </b>{{ $amount_in_words }} RUPEES Rs Only /-</small>
</div>


<div class="text-end mb-4">
    
    <small>
        <b>For. {{ $company->name }}</b>
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
    <small class="text-muted">{{ $invoice->address }}</small>
</div>

</section>

@endsection

