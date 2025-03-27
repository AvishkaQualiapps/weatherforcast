<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhetherController extends Controller
{
    public function getWeather($city)
    {
        try {
            $apiKey = env('WEATHER_API_KEY');
            $url = "http://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric";


            $response = Http::get($url);


            if ($response->successful()) {
                $weatherData = $response->json();

                return response()->json([
                    'temperature' => $weatherData['main']['temp'],
                    'description' => $weatherData['weather'][0]['description'],
                    'city' => $weatherData['name'],
                    'humidity' => $weatherData['main']['humidity'],
                    'wind_speed' => $weatherData['wind']['speed']
                ]);
            } else {

                Log::error('Weather API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'error' => 'Unable to fetch weather data',
                    'details' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {

            Log::error('Weather Controller Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function getWeatherForecast($city)
    {
        try {
            $apiKey = env('WEATHER_API_KEY');
            $url = "http://api.openweathermap.org/data/2.5/forecast?q={$city}&appid={$apiKey}&units=metric";

            $response = Http::get($url);

            if ($response->successful()) {
                $forecastData = $response->json();

                return response()->json($forecastData);
            } else {
                Log::error('Weather API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'error' => 'Unable to fetch weather forecast data',
                    'details' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Weather Controller Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
