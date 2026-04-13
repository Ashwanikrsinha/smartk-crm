<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Tally;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('tally')->group(function(){


    Route::apiResource('ledgers', Tally\LedgerController::class)->only(['index', 'store']);

    Route::apiResource('items', Tally\ItemController::class)->only(['index', 'store']);

    Route::apiResource('sales', Tally\SaleController::class)->only(['index', 'store']);

    Route::apiResource('purchases', Tally\PurchaseController::class)->only(['index', 'store']);

    Route::apiResource('stocks', Tally\StockController::class)->only(['index', 'store']);

    Route::apiResource('payments', Tally\PaymentController::class)->only(['index', 'store']);

   Route::apiResource('receipts', Tally\ReceiptController::class)->only(['index', 'store']);

   
});


// for whatsapp testing
Route::get('testing', function(Request $request){
    
    return response()->json([
        'message' => "whatsapp message text : {$request->text}",
        'stock' => App\Models\Tally\Stock::find(1)
    ]);
    
});





