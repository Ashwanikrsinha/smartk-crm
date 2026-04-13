@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
    <h5>Receipt</h5>
    <a href="{{ route('tally.receipts.index') }}" class="btn btn-sm btn-secondary">Back</a>
</header>

<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-calendar me-1 bg-primary text-white rounded p-1"></i>
        Receipt
    </header>
    <div class="card-body p-0">
        <section class="table-responsive mb-4">
            <table class="table" style="min-width: 60rem;">
                <tbody>
                    <tr>
                        <th class="ps-3">Voucher Number</th>
                        <td>{{ $receipt->voucher_number }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Vouher Date</th>
                        <td>
                            {{ $receipt->voucher_date->format('d M, Y') }}
                        </td>
                    </tr>
                    <tr>
                        <th class="ps-3">Voucher Type</th>
                        <td>{{ $receipt->voucher_type }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Voucher Master ID</th>
                        <td>{{ $receipt->voucher_master_id }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Voucher Alter ID</th>
                        <td>{{ $receipt->voucher_alter_id }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Reference No.</th>
                        <td>{{ $receipt->reference_number }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Reference Type</th>
                        <td>{{ $receipt->reference_type }}</td>
                    </tr>
                    
                    <tr>
                        <th class="ps-3">Company Name</th>
                        <td>{{ $receipt->company_name }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Amount</th>
                        <td>{{ $receipt->amount }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Payment Mode</th>
                        <td>{{ $receipt->payment_mode }}</td>
                    </tr>

                    <tr>
                        <th class="ps-3">Cheque No.</th>
                        <td>{{ $receipt->cheque_number }}</td>
                    </tr>


                    <tr>
                        <th class="ps-3">Cheque Date</th>
                        <td>{{ $receipt->cheque_date->format('d M, Y') }}</td>
                    </tr>


                    <tr>
                        <th class="ps-3">Bank Name</th>
                        <td>{{ $receipt->bank_name }}</td>
                    </tr>

                    <tr>
                        <th class="ps-3">Debit Ledger</th>
                        <td>{{ $receipt->debit_ledger }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Credit Ledger</th>
                        <td>{{ $receipt->credit_ledger }}</td>
                    </tr>

                </tbody>
            </table>
        </section>
    </div>
</div>



@endsection