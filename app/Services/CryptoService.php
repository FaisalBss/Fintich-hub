<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
class CryptoService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.coingecko.key');
        $this->baseUrl = 'https://api.coingecko.com/api/v3/';
    }


    public function getCryptoPrices()
    {
        try {
            $response = Http::withHeaders([
                'x-cg-demo-api-key' => $this->apiKey
            ])->get($this->baseUrl . 'coins/markets', [
                'vs_currency' => 'usd',
                'order' => 'market_cap_desc',
                'per_page' => 10,
                'page' => 1,
                'sparkline' => false
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Failed to fetch data from CoinGecko'
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
