<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use OpenAI\Laravel\Facades\OpenAI;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
