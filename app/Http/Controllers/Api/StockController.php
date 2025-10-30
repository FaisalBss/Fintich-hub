<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function show(string $symbol)
    {
        $data = $this->stockService->getStockQuote($symbol);
        return response()->json($data);
    }
}
