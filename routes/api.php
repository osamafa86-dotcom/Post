<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix('v1')->group(function () {
    // payment routes
    Route::post('payment/store', [PaymentController::class, 'capture'])->name('payment.capture');
    Route::post('payment/verify', [PaymentController::class, 'verify'])->name('payment.verify');
});
