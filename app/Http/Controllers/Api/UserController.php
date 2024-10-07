<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{

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
