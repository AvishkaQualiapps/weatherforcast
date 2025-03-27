<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WhetherController;





Route::post('/register', [AuthController::class, 'register'])->name('user.register');
Route::post('/login', [AuthController::class, 'login'])->name('login')->name('user.login');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout'])->name('user.logout');


Route::middleware('auth:sanctum')->get('/weather/{city}', [WhetherController::class, 'getWeather'])->name('city.weather');

Route::middleware('auth:sanctum')->get('/weatherforcast/{city}', [WhetherController::class, 'getWeatherForecast'])->name('city.weather.forecast');

Route::get('/test', function() {
    return response()->json(['message' => 'API is working']);
});

// Route::get('/weather/{city}', [WhetherController::class, 'getWeather']);
Route::get('/weather', function() {
    return response()->json(['message' => 'API is working']);
});
