<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiRegisterUserController;
use App\Http\Controllers\Api\UserController;

// 会員登録
Route::post('/register', [ApiRegisterUserController::class, 'store'])->name('api.user.register');
// ログイン（トークン発行）
Route::post('/token', [UserController::class, 'token'])->name('api.user.token');

/**
 * 認証必須API
 */
Route::middleware(['auth:sanctum',])->group(function () {
    // ユーザー情報を返すAPI
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    // ログアウト（トークン削除）
    Route::delete('/token', [UserController::class, 'deleteToken'])->name('api.user.token.delete');
});