<?php

namespace PrayerTimes;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class PrayerTimes
{
    public static function getTimes($country, $region, $city, $date = null, $days = 1)
    {
        $client = new Client();
        $baseUrl = "https://vakit.vercel.app/api/timesFromPlace";

        $queryParams = [
            'country' => $country,
            'region' => $region,
            'city' => $city,
            'date' => $date ?? now()->format('Y-m-d'),
            'days' => $days,
            'timezoneOffset' => 180,
            'calculationMethod' => 'Turkey',
        ];

        $cacheKey = 'prayer-times-' . md5(json_encode($queryParams));

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($client, $baseUrl, $queryParams) {
            try {
                $response = $client->get($baseUrl, ['query' => $queryParams]);
                return json_decode($response->getBody()->getContents(), true);
            } catch (\Exception $e) {
                return ['error' => 'Namaz vakitleri alınamıyor.'];
            }
        });
    }
}
