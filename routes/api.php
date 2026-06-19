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
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/account/{account_id}', [TransactionController::class, 'getByAccount']);
    Route::get('/transactions/repayment/{account_id}', [TransactionController::class, 'getRepaymentHistory']);
    Route::post('/transactions/repayment', [TransactionController::class, 'processRepayment']);
});

Route::post('/auth/login', [TransactionController::class, 'login']);
