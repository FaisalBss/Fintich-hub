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

            if ($response->successful() && isset($response->json()['Global Quote']['01. symbol'])) {

                $quote = $response->json()['Global Quote'];

                $price = (float) $quote['05. price'];
                $change = (float) $quote['09. change'];
                $status = 'unchanged';

                if ($change > 0) {
                    $status = 'up';
                } elseif ($change < 0) {
                    $status = 'down';
                }

                return [
                    'symbol' => $quote['01. symbol'],
                    'price' => $price,
                    'change' => $change,
                    'status' => $status
                ];
            }

            return ['status' => 'error', 'message' => 'Failed to fetch stock data'];

        } catch (\Exception $e) {
            report($e);
            return ['status' => 'error', 'message' => 'Service connection failed.'];
        }
    }


    public function getLatestPrice(string $symbol): ?float
    {
        $quote = $this->getStockQuote($symbol);

        if (isset($quote['price'])) {
            return (float) $quote['price'];
        }

        return null;
    }


    public function getTopGainers()
    {
        try {
            $response = Http::get($this->baseUrl, [
                'function' => 'TOP_GAINERS_LOSERS',
                'apikey' => $this->apiKey
            ]);

            if ($response->successful() && isset($response->json()['top_gainers'])) {
                return [
                    'status' => 'success',
                    'data' => array_slice($response->json()['top_gainers'], 0, 20)
                ];
            }
            return ['status' => 'error', 'message' => 'Failed to fetch top gainers data'];

        } catch (\Exception $e) {
            report($e);
            return ['status' => 'error', 'message' => 'Service connection failed.'];
        }
    }

    public function getIpoCalendar()
    {
        try {
            $response = Http::get($this->baseUrl, [
                'function' => 'IPO_CALENDAR',
                'apikey' => $this->apiKey
            ]);

            if ($response->successful()) {

                $csvData = $response->body();
                $lines = explode(PHP_EOL, $csvData);
                $headers = str_getcsv(array_shift($lines));
                $data = [];

                foreach ($lines as $line) {
                    if (empty(trim($line))) continue;
                    $data[] = array_combine($headers, str_getcsv($line));
                }

                return [
                    'status' => 'success',
                    'data' => $data
                ];
            }
            return ['status' => 'error', 'message' => 'Failed to fetch IPO calendar data'];

        } catch (\Exception $e) {
            report($e);
            return ['status' => 'error', 'message' => 'Service connection failed.'];
        }
    }
}

