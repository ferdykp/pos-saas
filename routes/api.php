<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentCallbackController; // <--- Pastikan import ini ada

// Rute Webhook Penerima Sinyal Lunas dari Midtrans
// Route::post('/api/midtrans/callback', [App\Http\Controllers\PaymentCallbackController::class, 'handleNotification']);
// Route::post('/api/midtrans/callback', [PaymentCallbackController::class, 'handleNotification']);
Route::post('/midtrans/callback', [PaymentCallbackController::class, 'handleNotification']);
