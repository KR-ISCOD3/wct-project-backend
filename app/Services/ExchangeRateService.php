<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    public function getExchangeRate()
    {
        try {
            $response = Http::get('https://api.exchangerate-api.com/v4/latest/USD'); // ✅ Use USD instead of KHR

            if ($response->successful()) {
                $data = $response->json();
                return $data['rates']['KHR'] ?? 4000; // ✅ Get KHR rate from USD
            }

            return 4000; // Default fallback rate if API fails
        } catch (\Exception $e) {
            return 4000; // Fallback rate in case of an error
        }
    }
}

