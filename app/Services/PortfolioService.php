<?php

namespace App\Services;

use App\Models\User;
use App\Models\Portfolio;

class PortfolioService
{

    public function addAsset(int $userId, string $symbol, string $type, float $quantity, float $purchasePrice): Portfolio
    {
        $asset = Portfolio::where('user_id', $userId)
                        ->where('symbol', $symbol)
                        ->where('type', $type)
                        ->first();

        if ($asset) {

            $newTotalQuantity = $asset->quantity + $quantity;
            $existingTotalCost = $asset->quantity * $asset->average_price;
            $newPurchaseCost = $quantity * $purchasePrice;

            $newAveragePrice = ($existingTotalCost + $newPurchaseCost) / $newTotalQuantity;

            $asset->update([
                'quantity' => $newTotalQuantity,
                'average_price' => $newAveragePrice,
            ]);

            return $asset->fresh();
        } else {
            return Portfolio::create([
                'user_id' => $userId,
                'symbol' => $symbol,
                'type' => $type,
                'quantity' => $quantity,
                'average_price' => $purchasePrice,
            ]);
        }
    }


    public function getUserPortfolio(User $user)
    {
        $user->loadMissing('wallet', 'portfolio');

        return [
            'balance' => $user->wallet->balance ?? 0.0,
            'assets' => $user->portfolio,
        ];
    }
}

