<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');

        $this->baseUrl = 'https://newsapi.org/v2';
    }

    public function getFinancialNews()
    {
        try {

            $response = Http::withHeaders([

                'X-Api-Key' => $this->apiKey
            ])->get($this->baseUrl . '/top-headlines', [
                'category' => 'business',
                'language' => 'en',
                'pageSize' => 20
            ]);

            if ($response->successful()) {
                return $response->json();
            }
            return ['status' => 'error', 'message' => 'Failed to fetch news data'];

        } catch (\Exception $e) {
            report($e);
            return ['status' => 'error', 'message' => 'Service connection failed.'];
        }
    }
}

