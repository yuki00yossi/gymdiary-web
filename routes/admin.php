<?php
/**
 * 管理者サイトのルーティング
 */
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;


Route::prefix('admin')->group(function () {
    Route::get('login', [Admin\AuthController::class, 'create'])->name('admin.login');
    Route::post('login', [Admin\AuthController::class, 'store'])->name('admin.login.store');

    // 要認証
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/', [Admin\AdminController::class, 'dashboard'])->name('admin.dashboard');

        // ユーザー管理関連
        Route::prefix('users')->group(function () {
            Route::get('admin', [Admin\AdminController::class, 'show'])->name('admin.users.admin.show');
            Route::get('admin/{id}', [Admin\AdminController::class, 'detail'])->name('admin.users.admin.detail');

            Route::get('user', [Admin\UserController::class, 'show'])->name('admin.users.user.show');
            Route::get('user/{id}', [Admin\UserController::class, 'detail'])->name('admin.users.user.detail');
        });

        Route::post('logout', [Admin\AuthController::class, 'destroy'])->name('admin.logout');
    });
});
