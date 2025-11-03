<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletService
{

    public function deposit(User $user, float $amount): array
    {
        DB::beginTransaction();
        try {
            $wallet = $user->wallet()->firstOrCreate(['user_id' => $user->id]);

            $wallet->increment('balance', $amount);

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Deposit successful.',
                'new_balance' => $wallet->balance,
                'code' => 200
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return [
                'status' => 'error',
                'message' => 'An error occurred during deposit.',
                'code' => 500
            ];
        }
    }


    public function hasSufficientBalance(User $user, float $amountToCheck): bool
    {
        $wallet = $user->wallet;

        if (!$wallet) {
            return false;
        }

        return $wallet->balance >= $amountToCheck;
    }
}

