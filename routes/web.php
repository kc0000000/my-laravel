<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

Route::get('/', [UserController::class, 'index']);
Route::get('/user-form', [UserController::class, 'showForm']);
Route::post('/user', [UserController::class, 'store']);
// Route::get('/users', [UserController::class, 'fetchUsers']);

// web.php or api.php
Route::get('/users', [UserController::class, 'getUsers'])->name('users.index');

Route::get('/user/{id}', [UserController::class, 'show']);

// routes/web.php
Route::post('/user/{id}', [UserController::class, 'update']);



