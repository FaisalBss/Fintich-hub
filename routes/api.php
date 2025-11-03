<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CryptoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\TradeController;


Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/cryptos', [CryptoController::class, 'index']);

    Route::get('/stocks/{symbol}', [StockController::class, 'show']);
    Route::get('/stocks/search/top-gainers', [StockController::class, 'topGainers']);
    Route::get('/stocks/search/ipo-calendar', [StockController::class, 'ipoCalendar']);

    Route::get('/currencies', [CurrencyController::class, 'index']);

    Route::get('/news', [NewsController::class, 'index']);

    Route::post('/wallet/deposit', [WalletController::class, 'deposit']);

    Route::post('/trade/buy', [TradeController::class, 'initiateBuy']);
    Route::post('/trade/confirm', [TradeController::class, 'confirmBuy']);

    Route::post('/logout', [AuthController::class, 'logout']);

});

