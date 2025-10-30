<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyService
{
    protected $baseUrl;
    protected $apiKey;
    public function __construct()
    {
        $this->apiKey = config('services.exchangerate.key');
        $this->baseUrl = 'https://v6.exchangerate-api.com/v6/';
    }

    public function getLatestRates(string $baseCurrency = 'USD')
    {
        try {
            $url = $this->baseUrl . $this->apiKey . '/latest/' . $baseCurrency;

            $response = Http::get($url);

            if ($response->successful() && $response->json()['result'] === 'success') {
                return $response->json();
            }

            $errorType = $response->json()['error-type'] ?? 'Unknown error';
            return [
                'status' => 'error',
                'message' => 'Failed to fetch data from ExchangeRate-API.',
                'provider_status_code' => $response->status(),
                'provider_error' => $errorType
            ];

        } catch (\Exception $e) {
            report($e);
            return ['status' => 'error', 'message' => 'Service connection failed.'];
        }
    }
}

