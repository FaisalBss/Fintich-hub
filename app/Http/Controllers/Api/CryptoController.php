<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CryptoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class CryptoController extends Controller
{
    protected $cryptoService;

    public function __construct(CryptoService $cryptoService)
    {
        $this->cryptoService = $cryptoService;
    }

    public function index(): JsonResponse
    {

        $data = $this->cryptoService->getCryptoPrices();


        return response()->json($data);
    }
}
