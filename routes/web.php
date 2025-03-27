<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::middleware('auth:sanctum')->get('/' , function(){
    return Auth::check()
        ? response()->json(['message' => 'User is authenticated'])
        : response()->json(['message' => 'User is not authenticated'], 401);
})->name('auth.check');
