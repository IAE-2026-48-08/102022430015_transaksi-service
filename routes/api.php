<?php

use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Transaction Service
| Standard Integration Contract (IAE-T2)
|--------------------------------------------------------------------------
*/

Route::middleware('check.api.key')->prefix('v1')->group(function () {

    // Collection: GET semua transaksi
    Route::get('/transactions', [TransactionController::class, 'index']);

    // Resource: GET transaksi by account
    Route::get('/transactions/account/{account_id}', [TransactionController::class, 'getByAccount']);

    // Resource: GET riwayat cicilan by account
    Route::get('/transactions/repayment/{account_id}', [TransactionController::class, 'getRepaymentHistory']);

    // Action: POST eksekusi pembayaran cicilan
    Route::post('/transactions/repayment', [TransactionController::class, 'processRepayment']);
});
