<?php

namespace App\Services;

use App\Models\User;
use App\Models\PendingTrade;
use App\Services\StockService;
use App\Services\CryptoService;
use App\Services\WalletService;
use App\Models\Portfolio;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class TradeService
{
    protected $stockService;
    protected $cryptoService;
    protected $walletService;

    public function __construct(
        StockService $stockService,
        CryptoService $cryptoService,
        WalletService $walletService
    ) {
        $this->stockService = $stockService;
        $this->cryptoService = $cryptoService;
        $this->walletService = $walletService;
    }

    public function initiateBuy(User $user, string $symbol, float $quantity, string $type): array
    {
        $price = null;
        if ($type === 'stock') {
            $price = $this->stockService->getLatestPrice($symbol);
        } else {
            $price = $this->cryptoService->getLatestPrice(strtolower($symbol));
        }

        if (!$price || $price <= 0) {
            return ['status' => 'error', 'message' => 'Could not fetch a valid price for symbol.', 'code' => 404];
        }

        $totalCost = $price * $quantity;

        if (!$this->walletService->hasSufficientBalance($user, $totalCost)) {
            return ['status' => 'error', 'message' => 'Insufficient funds.', 'code' => 400];
        }

        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        PendingTrade::where('user_id', $user->id)->delete();

        $pendingTrade = PendingTrade::create([
            'user_id' => $user->id,
            'symbol' => $symbol,
            'type' => $type,
            'quantity' => $quantity,
            'price_per_unit' => $price,
            'total_cost' => $totalCost,
            'otp' => $otp,
            'expires_at' => $expiresAt,
        ]);

        try {
            Mail::to($user->email)->queue(new SendOtpMail($otp));
            return [
                'status' => 'success',
                'message' => 'Purchase initiated. Check your email for OTP to confirm.',
                'total_cost' => $totalCost,
                'code' => 200
            ];
        } catch (\Exception $e) {
            report($e);
            return ['status' => 'error', 'message' => 'Failed to send OTP.', 'code' => 500];
        }
    }


    public function confirmBuy(User $user, string $otp): array
    {
        $trade = $user->pendingTrade()->first();

        if (!$trade) {
            return ['status' => 'error', 'message' => 'No pending trade found.', 'code' => 404];
        }

        if ($trade->otp !== $otp) {
            return ['status' => 'error', 'message' => 'Invalid OTP.', 'code' => 401];
        }

        if (now()->gt($trade->expires_at)) {
            $trade->delete();
            return ['status' => 'error', 'message' => 'OTP expired. Please initiate purchase again.', 'code' => 400];
        }

        DB::beginTransaction();
        try {
            $wallet = $user->wallet;
            if ($wallet->balance < $trade->total_cost) {
                DB::rollBack();
                return ['status' => 'error', 'message' => 'Insufficient funds (check failed again).', 'code' => 400];
            }
            $wallet->decrement('balance', $trade->total_cost);

            $portfolioItem = $user->portfolio()
                ->firstOrCreate(
                    ['symbol' => $trade->symbol, 'type' => $trade->type],
                    ['quantity' => 0, 'average_price' => 0]
                );

            $newQuantity = $portfolioItem->quantity + $trade->quantity;
            $newTotalValue = ($portfolioItem->average_price * $portfolioItem->quantity) + $trade->total_cost;
            $newAveragePrice = $newTotalValue / $newQuantity;

            $portfolioItem->update([
                'quantity' => $newQuantity,
                'average_price' => $newAveragePrice
            ]);

            $trade->delete();

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Purchase confirmed successfully!',
                'new_balance' => $wallet->balance,
                'portfolio_item' => $portfolioItem,
                'code' => 200
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return ['status' => 'error', 'message' => 'An error occurred during final transaction.', 'code' => 500];
        }
    }
}

