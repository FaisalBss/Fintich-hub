<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletService
{

    public function deposit(User $user, float $amount): array
    {
        try {
            DB::beginTransaction();

            $wallet = $user->wallet()->firstOrCreate(['user_id' => $user->id]);

            $wallet->increment('balance', $amount);
            $wallet->save();

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
                'message' => 'Deposit failed. Please try again.',
                'code' => 500
            ];
        }
    }
}
