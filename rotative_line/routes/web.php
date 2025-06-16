<?php

use App\Http\Controllers\PpsimulationAPI;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('PpsimulationAPI', [PpsimulationAPI::class, "index"]);