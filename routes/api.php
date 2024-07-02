<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Log::info('CSRF Token (csrf_token()): ' . csrf_token());
Log::info('CSRF Token (session token): ' . $request->session()->token());
Log::info('Session ID: ' . $request->session()->getId());


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {

    Log::info('Authenticated User: ' . $request->user());
    return $request->user();
});
