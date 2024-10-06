<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiRegisterUserController;

Route::post('/register', [ApiRegisterUserController::class, 'store'])->name('api.user.register');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
