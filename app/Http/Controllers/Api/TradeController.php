<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TradeService;
use App\Http\Requests\Trade\InitiateBuyRequest;
use App\Http\Requests\Trade\ConfirmBuyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TradeController extends Controller
{
    protected $tradeService;

    public function __construct(TradeService $tradeService)
    {
        $this->tradeService = $tradeService;
    }

    public function initiateBuy(InitiateBuyRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $result = $this->tradeService->initiateBuy(
            $user,
            $validated['symbol'],
            (float) $validated['quantity'],
            $validated['type']
        );

        return response()->json($result, $result['code']);
    }


    public function confirmBuy(ConfirmBuyRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $result = $this->tradeService->confirmBuy($user, $validated['otp']);

        return response()->json($result, $result['code']);
    }
}

