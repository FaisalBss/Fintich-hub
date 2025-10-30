<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use App\Http\Requests\Wallet\DepositRequest;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }


    public function deposit(DepositRequest $request)
    {
        $user = $request->user();

        $amount = $request->validated()['amount'];

        $result = $this->walletService->deposit($user, $amount);

        return response()->json($result, $result['code']);
    }
}
