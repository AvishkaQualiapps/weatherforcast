<?php

use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/' , function(){
    return view('welcome');
} );

