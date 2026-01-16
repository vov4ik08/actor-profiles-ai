<?php

use App\Http\Controllers\Api\ActorController;
use Illuminate\Support\Facades\Route;

Route::get('/actors', [ActorController::class, 'index']);
Route::post('/actors', [ActorController::class, 'store']);
Route::get('/actors/prompt-validation', [ActorController::class, 'promptValidation']);

