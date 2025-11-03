<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PortfolioService;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    protected $portfolioService;

    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }


    public function index(Request $request)
    {
        $portfolioData = $this->portfolioService->getUserPortfolio($request->user());

        return response()->json([
            'status' => 'success',
            'data' => $portfolioData
        ], 200);
    }
}
