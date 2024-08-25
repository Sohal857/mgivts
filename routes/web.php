<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleDataController;

Route::get('/vehicles', [VehicleDataController::class, 'index']);