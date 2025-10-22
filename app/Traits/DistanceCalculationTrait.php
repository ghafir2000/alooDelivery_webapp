<?php

namespace App\Traits;

use App\Utils\Helpers; // You may need to import your Helpers class if getWebConfig is in it
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait DistanceCalculationTrait
{
    /**
     * Calculates the driving distance between two points using Google's Distance Matrix API.
     *
     * @param float $lat1 Latitude of the origin point
     * @param float $lon1 Longitude of the origin point
     * @param float $lat2 Latitude of the destination point
     * @param float $lon2 Longitude of the destination point
     * @return float|null The driving distance in kilometers, or null if the request fails.
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): ?float
    {
        // --- THIS IS THE CHANGE ---
        // Get the secure, server-side API key from the business_settings table
        // using your application's helper function.
        logger("Calculating distance between lat1: $lat1, lon1: $lon1 and lat2: $lat2, lon2: $lon2");
        $apiKey = getWebConfig(name: 'map_api_key_server');

        if (empty($apiKey)) {
            Log::error('Google Maps Server API Key is not found in the business_settings table.');
            return null;
        }

        // Prepare the origin and destination strings in the format Google requires (lat,lng)
        $origin = $lat1 . ',' . $lon1;
        $destination = $lat2 . ',' . $lon2;

        // Make the API request using Laravel's HTTP Client
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $origin,
            'destinations' => $destination,
            'key' => $apiKey,
            'units' => 'metric', // ensures the result is in kilometers
        ]);

        $data = $response->json();

        // Check if the API request was successful and if a route was found
        if (
            $response->successful() &&
            isset($data['status']) &&
            $data['status'] == 'OK' &&
            isset($data['rows'][0]['elements'][0]['status']) &&
            $data['rows'][0]['elements'][0]['status'] == 'OK'
        ) {
            // The distance is returned in meters. Convert it to kilometers.
            $distanceInMeters = $data['rows'][0]['elements'][0]['distance']['value'];
            $distanceInKm = $distanceInMeters / 1000;

            return round($distanceInKm, 2);
        }

        // If something went wrong, log the error and return null
        Log::error('Google Distance Matrix API Error:', [
            'status' => $data['status'] ?? 'Unknown',
            'error_message' => $data['error_message'] ?? 'No error message provided.',
        ]);

        return null;
    }
}