<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [ UserController::class, 'store']);

Route::get('/posts', [ PostController::class, 'index']);
Route::get('/users', [ UserController::class, 'index']);

Route::post('/forgot', [ PasswordController::class, 'ForgotPassword']);
Route::post('/reset/{token}', [ PasswordController::class, 'ResetPassword']);

Route::middleware('auth:sanctum')->group(function(){
  Route::post('/users', [ UserController::class, 'store']);
  Route::get('/users/{user}', [ UserController::class, 'show']);
  Route::put('/users/{user}', [ UserController::class, 'update']);
  Route::delete('/users/{user}', [ UserController::class, 'destroy']);
  Route::get('/users-export', [ UserController::class, 'export' ]);
  Route::post('/users-import', [ UserController::class, 'import']);
  Route::get('/images/{filename}', [ UserController::class, 'image']);

  Route::post('/posts', [ PostController::class, 'store']);
  Route::get('/posts/{post}', [ PostController::class, 'show']);
  Route::put('/posts/{post}', [ PostController::class, 'update']);
  Route::delete('/posts/{post}', [ PostController::class, 'destroy']);
  Route::get('/posts-export', [ PostController::class, 'export']);
  Route::post('/posts-import', [ PostController::class, 'import']);

  Route::post('/change-password', [ PasswordController::class, 'changePassword']);

  Route::post('/logout', [AuthController::class, 'logout']);
});
