<?php

namespace App\Http\Controllers\Api\Tally;

use App\Http\Controllers\Controller;
use App\Models\Tally\Receipt;
use Illuminate\Http\Request;
use App\Http\Requests\Tally\ReceiptStoreRequest;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $receipts = Receipt::select();
        
        return DataTables::of($receipts)
            ->editColumn('voucher_date', function ($receipt) {
                return $receipt->voucher_date->format('d M, Y');
            })
            ->addColumn('actions', function ($receipt){
                return view('tally.receipts.button')->with('receipt', $receipt);
            })
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReceiptStoreRequest $request)
    {
        DB::transaction(function() use($request){
            Receipt::createReceipts($request);
        });
        
        return response()->json(['message' => 'receipts vouchers are created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function show(Receipt $receipt)
    {
       //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receipt $receipt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receipt $receipt)
    {
        //
    }
}
