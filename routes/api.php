<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiRegisterUserController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkoutController;
use App\Http\Controllers\Api\MealController;

// 会員登録
Route::post('/user', [UserController::class, 'store'])->name('api.user.store');
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

    /** トレーニングログ関連のAPI */
    // 取得
    Route::get('/workouts', [WorkoutController::class, 'retrieve'])->name('api.workout.retrieve');
    // 保存
    Route::post('/workouts', [WorkoutController::class, 'store'])->name('api.workout.store');
    // 更新
    Route::put('/workouts/{workout_id}', [WorkoutController::class, 'update'])->name('api.workout.update');
    // 削除
    Route::delete('/workouts/{workout_id}', [WorkoutController::class, 'destroy'])->name('api.workout.delete');

    /** 食事ログ関連のAPI */
    // 保存
    Route::post('/meals', [MealController::class, 'store'])->name('api.meal.store');
});