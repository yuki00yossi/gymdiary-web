<?php
/**
 * IP制限を行うミドルウェア
 *
 * app.envがproductionの時のみ動作する
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\IpUtils;

class FirewallMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.env') !== 'production' || $this->isAllowedIp(collect($request->getClientIps())->last())) {
            return $next($request);
        }

        throw new AuthorizationException(sprintf('Access denied from %s', collect($request->getClientIps())->last()));
    }

    /**
     * .envで指定された許可IPアドレスと、リクエスト元IPを比較する
     *
     * @param string $ip
     * @return bool
     */
    private function isAllowedIp(string $ip): bool
    {
        return IpUtils::checkIp($ip, explode(',', env('ALLOWED_IPS')));
    }
}
