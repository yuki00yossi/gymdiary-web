<?php
/**
 * 管理者サイトのルーティング
 */
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;


Route::prefix('admin')->group(function () {
    Route::get('login', [Admin\AuthController::class, 'create'])->name('admin.login');
    Route::post('login', [Admin\AuthController::class, 'store'])->name('admin.login.store');
    // Route::get('logout', [Admin\LoginController::class, 'logout'])->name('admin.login.logout');

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/', [Admin\AdminController::class, 'dashboard'])->name('admin.dashboard');
    });

    // Route::get('/',[Admin\AdminController::class, 'dashboard'])->name('admin.dashboard');
});
