<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    /**
     * 会員登録API
     *
     * 新しいユーザーを登録し、登録完了後にイベントを発行する。
     * リクエストには`username`、`email`、`name`、および`password`が必要。
     * 成功時には、新規ユーザーの情報を含むJSONレスポンスを返す。
     *
     * @param \Illuminate\Http\Request $request HTTPリクエストオブジェクト
     *
     * @return \Illuminate\Http\JsonResponse 新規ユーザーのデータを含むJSONレスポンス
     *
     * @throws \Illuminate\Validation\ValidationException バリデーションエラー時にスローされる。
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

    /**
     * ユーザーのログインを行い、アクセストークンを発行
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException バリデーションエラー時にスロー
     */
    public function token(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // ユーザーの認証
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'ログイン情報が正しくありません。'
            ], 401);
        }

        // トークンを発行
        $token = $user->createToken('gym-diary-token')->plainTextToken;

        // 成功レスポンスを返す
        return response()->json([
            'message' => 'You are loged in!',
            'token' => $token
        ], 200);
    }

    /**
     * 認証済みユーザーのトークンを無効化してログアウト
     *
     * @param Request $request HTTPリクエストオブジェクト
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteToken(Request $request)
    {
        // 認証済みユーザーのトークンを削除
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Deleted token successfully.'
        ], 200);
    }
}
