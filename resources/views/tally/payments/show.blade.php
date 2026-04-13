@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
    <h5>Payment</h5>
    <a href="{{ route('tally.payments.index') }}" class="btn btn-sm btn-secondary">Back</a>
</header>

<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-calendar me-1 bg-primary text-white rounded p-1"></i>
        Payment
    </header>
    <div class="card-body p-0">
        <section class="table-responsive mb-4">
            <table class="table" style="min-width: 60rem;">
                <tbody>
                    <tr>
                        <th class="ps-3">Voucher Number</th>
                        <td>{{ $payment->voucher_number }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Vouher Date</th>
                        <td>
                            {{ $payment->voucher_date->format('d M, Y') }}
                        </td>
                    </tr>
                    <tr>
                        <th class="ps-3">Voucher Type</th>
                        <td>{{ $payment->voucher_type }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Voucher Master ID</th>
                        <td>{{ $payment->voucher_master_id }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Voucher Alter ID</th>
                        <td>{{ $payment->voucher_alter_id }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Reference No.</th>
                        <td>{{ $payment->reference_number }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Reference Type</th>
                        <td>{{ $payment->reference_type }}</td>
                    </tr>
                    
                    <tr>
                        <th class="ps-3">Company Name</th>
                        <td>{{ $payment->company_name }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Amount</th>
                        <td>{{ $payment->amount }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Payment Mode</th>
                        <td>{{ $payment->payment_mode }}</td>
                    </tr>

                    <tr>
                        <th class="ps-3">Cheque No.</th>
                        <td>{{ $payment->cheque_number }}</td>
                    </tr>


                    <tr>
                        <th class="ps-3">Cheque Date</th>
                        <td>{{ $payment->cheque_date->format('d M, Y') }}</td>
                    </tr>


                    <tr>
                        <th class="ps-3">Bank Name</th>
                        <td>{{ $payment->bank_name }}</td>
                    </tr>

                    <tr>
                        <th class="ps-3">Debit Ledger</th>
                        <td>{{ $payment->debit_ledger }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Credit Ledger</th>
                        <td>{{ $payment->credit_ledger }}</td>
                    </tr>

                </tbody>
            </table>
        </section>
    </div>
</div>



@endsection