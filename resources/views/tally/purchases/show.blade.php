@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
    <h5>Sale</h5>
    <a href="{{ route('tally.purchases.index') }}" class="btn btn-sm btn-secondary">Back</a>
</header>

<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-calendar me-1 bg-primary text-white rounded p-1"></i>
        Sale
    </header>
    <div class="card-body p-0">
        <section class="table-responsive mb-4">
            <table class="table" style="min-width: 60rem;">
                <tbody>
                    <tr>
                        <th class="ps-3">Voucher Number</th>
                        <td>{{ $purchase->voucher_number }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Vouher Date</th>
                        <td>
                            {{ $purchase->voucher_date->format('d M, Y') }}
                        </td>
                    </tr>
                    <tr>
                        <th class="ps-3">Voucher Type</th>
                        <td>{{ $purchase->voucher_type }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">GU ID</th>
                        <td>{{ $purchase->gu_id }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Party Name</th>
                        <td>{{ $purchase->party_name }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Ledger Name</th>
                        <td>{{ $purchase->ledger_name }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Entered By</th>
                        <td>{{ $purchase->entered_by }}</td>
                    </tr>

                </tbody>
            </table>
        </section>
    </div>
</div>



<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-calendar me-1 bg-primary text-white rounded p-1"></i> 
        Buyer Address List
    </header>
    <div class="card-body p-0">
       @if (isset($purchase->buyer_address_list))  
        <section class="table-responsive mb-4">
            <table class="table" style="min-width: 60rem;">
                <tbody>
                    @foreach ($purchase->buyer_address_list as $buyer)   
                        <tr>
                            <th>Basic Buy Address</th>
                            <td>{{ $buyer['BASICBUYERADDRESS'] ?? 'NOT GIVEN' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
       @endif
    </div>
</div>



<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-calendar me-1 bg-primary text-white rounded p-1"></i> 
        Inventory List
    </header>
    <div class="card-body p-0">

        @if (isset($purchase->inventories_list))  
        <section class="table-responsive mb-4">
            <table class="table" style="min-width: 60rem;">
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Discount</th>
                        <th>Stock Item Name</th>
                        <th>Billed Quantity</th>
                        <th>Rate</th>
                        <th>Accounting Location List</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->inventories_list as $inventory)
                        <tr>
                            <td>{{ $inventory['AMOUNT'] }}</td>
                            <td>{{ $inventory['DISCOUNT'] }}</td>
                            <td>{{ $inventory['STOCKITEMNAME'] }}</td>
                            <td>{{ $inventory['BILLEDQTY'] }}</td>
                            <td>{{ $inventory['RATE'] }}</td>
                            <td>
                                @if (isset($inventory['ACCOUNTINGALLOCATIONS.LIST']))
                                    <table class="table table-sm table-borderless">
                                        <tbody>
                                            @foreach ($inventory['ACCOUNTINGALLOCATIONS.LIST'] as $item)
                                                <th>Ledger Name</th>
                                                <td>{{ $item['LEDGERNAME'] }}</td>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
       @endif
    </div>
</div>



<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-calendar me-1 bg-primary text-white rounded p-1"></i> 
        ledger Entry List
    </header>
    <div class="card-body p-0">

        @if (isset($purchase->ledger_entries_list))  
        <section class="table-responsive mb-4">
            <table class="table" style="min-width: 60rem;">
                <thead>
                    <th>Ledger Name</th>
                    <th>Amount</th>
                    <th>Bill Location List</th>
                </thead>
                <tbody>
                    @foreach ($purchase->ledger_entries_list as $item)

                        <tr>
                            <td>{{ $item['LEDGERNAME'] }}</td>
                            <td>{{ $item['AMOUNT'] }}</td>
                            <td>
                                @if (isset($item['BILLALLOCATIONS.LIST']))
                                    <table class="table table-sm table-borderless">
                                        <thead>
                                            <th>Bill Type</th>
                                            <th>Amount</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($item['BILLALLOCATIONS.LIST'] as $item)
                                                <td>{{ $item['BILLTYPE'] }}</td>
                                                <td>{{ $item['AMOUNT'] }}</td>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
       @endif

    </div>
</div>

@endsection