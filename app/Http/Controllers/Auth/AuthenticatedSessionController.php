<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return response()->json(['msg' => 'logged in successfully.'], 200);
    }

    /**
     * Check Login Status.
     */
    public function check()
    {
        $is_member_login = Auth::guard('web')->check();
        $is_trainer_login = false;

        return response()->json([
            "memberLoggedIn" => $is_member_login,
            "trainerLoggedIn" => false
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'msg' => 'logout successfully.'
        ], 200);
    }
}
