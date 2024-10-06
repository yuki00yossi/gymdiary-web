<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class ApiRegisterUserController extends Controller
{

    /**
     * 会員登録API
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => ['bail', 'required', 'string', 'lowercase', 'max:255', 'unique:'.User::class],
            'email' => ['bail', 'required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'name' => ['bail', 'required', 'string', 'max:255'],
            'password' => ['bail', 'required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return response()->json($user, 201);
    }
}
