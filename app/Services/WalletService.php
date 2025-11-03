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

    public function withdraw(User $user, float $amount): Wallet
    {
        $wallet = $user->wallet;

        DB::transaction(function () use ($wallet, $amount) {
            $wallet = $wallet->lockForUpdate()->first();

            if ($wallet->balance < $amount) {
                throw new \Exception('Insufficient funds during final transaction.');
            }

            $wallet->decrement('balance', $amount);
        });

        return $wallet->fresh();
    }
}

