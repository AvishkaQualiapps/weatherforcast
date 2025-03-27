<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
    public function getWeatherForecast(Request $request, $city)
    {
        try {
            $apiKey = env('WEATHER_API_KEY');
            $url = "http://api.openweathermap.org/data/2.5/forecast?q={$city}&appid={$apiKey}&units=metric";

            $response = Http::get($url);

            if ($response->successful()) {
                $forecastData = $response->json();
                $forecasts = $forecastData['list'];

                foreach ($forecasts as $forecast) {
                    $humidity = $forecast['main']['humidity'];
                    if ($humidity > 70) {
                        $forecast['main']['humidity'] = 'High';
                    } elseif ($humidity > 40) {
                        $forecast['main']['humidity'] = 'Medium';
                    } else {
                        $forecast['main']['humidity'] = 'Low';
                    }

                    $humiditylist[] = $forecast['main']['humidity'];
                    print_r($humiditylist);
                }


                $startDate = Carbon::parse($request->query('start_date'));
                $endDate = Carbon::parse($request->query('end_date'));


                $filteredForecasts = collect($forecasts)->filter(function ($forecast) use ($startDate, $endDate) {
                    $forecastDate = Carbon::parse($forecast['dt_txt']);


                    return $forecastDate->between($startDate, $endDate);
                });


                $formattedForecasts = $filteredForecasts->map(function ($forecast) {
                    return [
                        'datetime' => Carbon::parse($forecast['dt_txt'])->toDateTimeString(),
                        'temperature' => $forecast['main']['temp'],
                        'description' => $forecast['weather'][0]['description'],
                        'humidity' => $forecast['main']['humidity'],
                        'wind_speed' => $forecast['wind']['speed']
                    ];
                });

                return response()->json([
                    'city' => $city,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'forecast' => $formattedForecasts
                ]);
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
