<?php

use App\Http\Controllers\ChapaPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/payment/failed', [ChapaPaymentController::class, 'failed'])->name('payment.failed');

Route::post('/payment/initiate', [ChapaPaymentController::class, 'initiatePayment'])->name('payment.initiate');
Route::get('/payment/callback', [ChapaPaymentController::class, 'paymentCallback'])->name('chapa.callback');

// Assuming your payment success route is defined in another controller
Route::get('/payment/success', [ChapaPaymentController::class, 'success'])->name('payment.success');
Route::get('/transactions', [ChapaPaymentController::class, 'getAllTransactions'])->name('transactions.index');
