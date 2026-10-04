<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'API Server for Mobile Ayam Geprek Rejo is running.',
        'status' => 'active'
    ]);
});
