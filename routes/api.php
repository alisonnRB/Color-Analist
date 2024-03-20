<?php

use App\Http\Controllers\Api\AnalistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('analist', [AnalistController::class, 'index']);