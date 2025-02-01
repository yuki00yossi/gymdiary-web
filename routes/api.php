<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiRegisterUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkoutController;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\WeightController;

// 会員登録
// Route::post('/user', [UserController::class, 'store'])->name('api.user.store');
// ログイン（トークン発行）
// Route::post('/token', [UserController::class, 'token'])->name('api.user.token');
Route::post('/user/login', [AuthenticatedSessionController::class, 'store'])->name('api.user.login');
Route::get('/auth/check', [AuthenticatedSessionController::class, 'check'])->name('api.auth.check');

/**
 * 認証必須API
 */
Route::middleware(['auth',])->group(function () {
    // ユーザー情報を返すAPI
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
    // ユーザーを有効化するAPI
    Route::post('/email/verify', [UserController::class, 'verify_email'])->name('api.user.email.verify');
});


Route::middleware(['auth:web', 'verified'])->group(function () {
    // ログアウトするAPI
    Route::post('/user/signout', [AuthenticatedSessionController::class, 'destroy'])->name('api.user.signout');

    // 体重取得API
    Route::get('/user/weight', [WeightController::class, 'get_daily'])->name('api.user.weight.daily');

    // 体重保存（当日）API
    Route::post('/user/weight', [WeightController::class, 'store_today'])->name('api.user.weight.store');

    /** トレーニングログ関連のAPI */
    // 取得
    Route::get('/workouts', [WorkoutController::class, 'retrieve'])->name('api.workout.retrieve');
    // 保存
    Route::post('/workouts', [WorkoutController::class, 'store'])->name('api.workout.store');
    // 更新
    Route::put('/workouts/{workout_id}', [WorkoutController::class, 'update'])->name('api.workout.update');
    // 削除
    Route::delete('/workouts/{workout_id}', [WorkoutController::class, 'destroy'])->name('api.workout.destroy');

    /** 食事ログ関連のAPI */
    // 取得
    Route::get('/meals/{userId}', [MealController::class, 'retrieve'])->name('api.meal.retrieve');
    // 保存
    Route::post('/meals', [MealController::class, 'store'])->name('api.meal.store');
    // 更新
    Route::put('/meals/{mealId}', [MealController::class, 'update'])->name('api.meal.update');
    // 削除
    Route::delete('/meals/{mealId}', [MealController::class, 'destroy'])->name('api.meal.destroy');
});