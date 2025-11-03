<?php

namespace App\Services;

use App\Models\User;
use App\Models\Portfolio;

class PortfolioService
{
    /**
     * إضافة أصل (سهم/عملة) إلى محفظة المستخدم
     * (الكود القديم موجود هنا)
     */
    public function addAsset(int $userId, string $symbol, string $type, float $quantity, float $purchasePrice): Portfolio
    {
        // 1. ابحث إذا كان المستخدم يمتلك هذا الأصل
        $asset = Portfolio::where('user_id', $userId)
                        ->where('symbol', $symbol)
                        ->where('type', $type)
                        ->first();

        if ($asset) {
            // --- الأصل موجود: قم بالتحديث ---

            // حساب متوسط السعر الجديد
            $newTotalQuantity = $asset->quantity + $quantity;
            $existingTotalCost = $asset->quantity * $asset->average_price;
            $newPurchaseCost = $quantity * $purchasePrice;

            $newAveragePrice = ($existingTotalCost + $newPurchaseCost) / $newTotalQuantity;

            $asset->update([
                'quantity' => $newTotalQuantity,
                'average_price' => $newAveragePrice,
            ]);

            return $asset->fresh(); // إرجاع النسخة المحدثة
        } else {
            // --- أصل جديد: قم بالإنشاء ---
            return Portfolio::create([
                'user_id' => $userId,
                'symbol' => $symbol,
                'type' => $type,
                'quantity' => $quantity,
                'average_price' => $purchasePrice, // السعر الحالي هو متوسط السعر
            ]);
        }
    }

    /**
     * (جديد) جلب بيانات المحفظة الكاملة للمستخدم
     * (الرصيد + الأصول)
     */
    public function getUserPortfolio(User $user)
    {
        // 1. جلب العلاقات من المودل (الذي جهزناه سابقاً)
        $user->loadMissing('wallet', 'portfolio');

        return [
            'balance' => $user->wallet->balance ?? 0.0,
            'assets' => $user->portfolio,
        ];
    }
}

