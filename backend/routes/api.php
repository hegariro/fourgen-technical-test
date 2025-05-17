<?php

use App\Http\Controllers\Auth\Api\LoginController;
use App\Http\Controllers\Auth\Api\RegisterController;
use App\Http\Controllers\Auth\Api\UserController;
use App\Http\Controllers\Pet\Api\PetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas públicas para registro y login API
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

// Rutas protegidas por Sanctum (requieren un token válido)
Route::middleware('auth:sanctum')->group(function () {

  Route::get('/user', [UserController::class, 'show']);
  Route::put('/user', [UserController::class, 'update']);
  Route::delete('/user', [UserController::class, 'destroy']);
  Route::put('/user/password', [UserController::class, 'updatePassword']);

  Route::get('/pets/all', [PetController::class, 'allPets']);
  Route::apiResource('pets', PetController::class);

  Route::post('/logout', [LoginController::class, 'logout']);
});
