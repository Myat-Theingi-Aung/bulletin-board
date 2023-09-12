<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function(){
  Route::get('/users', [ UserController::class, 'index']);
  Route::post('/users', [ UserController::class, 'store']);
  Route::get('/users/{user}', [ UserController::class, 'show']);
  Route::put('/users/{user}', [ UserController::class, 'update']);
  Route::delete('/users/{user}', [ UserController::class, 'destroy']);

  Route::get('/posts', [ PostController::class, 'index']);
  Route::post('/posts', [ PostController::class, 'store']);
  Route::get('/posts/{post}', [ PostController::class, 'show']);
  Route::put('/posts/{post}', [ PostController::class, 'update']);
  Route::delete('/posts/{post}', [ PostController::class, 'destroy']);

  Route::post('/logout', [AuthController::class, 'logout']);
});
