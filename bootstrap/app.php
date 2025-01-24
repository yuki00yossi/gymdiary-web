<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Http\Request;

use App\Http\Middleware\FirewallMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function (Request $request) {
            if (request()->routeIs('admin.*')) {
                return $request->expectsJson() ? null : route('admin.login');
            }
        });

        // 管理画面へのIP制限と、ベーシック認証を行うミドルウェア
        $middleware->appendToGroup('admin_firewall', [
            FirewallMiddleware::class,
        ]);

        // apiでもセッションを有効化するための設定
        $middleware->group('api', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class, // Rate Limit
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // ログインユーザー用のリダイレクト設定
        $middleware->redirectUsersTo(function () {
            if(Auth::guard('admin')) {
                return route('admin.dashboard');
            }
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
