<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class StockService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.alphavantage.key');
        $this->baseUrl = 'https://www.alphavantage.co/query';
    }

    public function getStockQuote(string $symbol)
    {
        try {
            $response = Http::get($this->baseUrl, [
                'function' => 'GLOBAL_QUOTE',
                'symbol' => $symbol,
                'apikey' => $this->apiKey
            ]);

            if ($response->successful()) {
                return $response->json();
            }
            return ['status' => 'error', 'message' => 'Failed to fetch stock data'];

        } catch (\Exception $e) {
            report($e);
            return ['status' => 'error', 'message' => 'Service connection failed.'];
        }
    }
}
