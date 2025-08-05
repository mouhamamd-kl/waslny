<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;

Route::middleware('auth:admin-api')->prefix('wallet')->group(function () {
    Route::get('/', [WalletController::class, 'show'])->name('admin.wallet.show');
    Route::post('/credit', [WalletController::class, 'credit'])->name('admin.wallet.credit');
    Route::post('/debit', [WalletController::class, 'debit'])->name('admin.wallet.debit');
});
