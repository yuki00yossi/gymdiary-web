<?php

namespace App\Http\Controllers\Api;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignupRequest;
use App\Models\User;

use App\Mail\MailVerificationCodeMail;

class UserController extends Controller
{
    /**
     * 会員登録API
     *
     * 新しいユーザーを登録し、登録完了後にイベントを発行する。
     * リクエストには`username`、`email`、`name`、および`password`が必要。
     * 成功時には、新規ユーザーの情報を含むJSONレスポンスを返す。
     *
     * @param App\Http\Requests\Auth\SignupRequest $request HTTPリクエストオブジェクト
     *
     * @return \Illuminate\Http\JsonResponse 新規ユーザーのデータを含むJSONレスポンス
     *
     * @throws \Illuminate\Validation\ValidationException バリデーションエラー時にスローされる。
     */
    public function store(SignupRequest $request)
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

        $request->authenticate();
        $request->session()->regenerate();

        // メアド確認コードの記載したメール送信
        $user->generateMailVerificationCode();
        Mail::to($user->email)
            ->send(new MailVerificationCodeMail($user->mail_verification_code, User::MAIL_VERIFY_CODE_EXPIRE_TIME));

        return response()->json($user, 201);
    }

    /**
     * 会員有効化（メアド検証）API
     *
     * @param Illuminate\Http\Request; $request HTTPリクエストオブジェクト
     *
     * @return \Illuminate\Http\JsonResponse 新規ユーザーのデータを含むJSONレスポンス
     *
     * @throws \Illuminate\Validation\ValidationException バリデーションエラー時にスローされる。
     */
    public function verify_email(Request $request)
    {
        $validated = $request->validate([
            'code' => ['bail', 'required', 'integer'],
        ]);

        if ($request->user()->isMailVerificationCodeValid($validated['code'])) {
            $request->user()->markEmailAsVerified();

            return response()->json([
                'email' => $request->user()->email,
                'msg' => 'success',
            ], 200);
        }

        return response()->json('not valid', 400);
    }

    /**
     * ログアウトAPI
     *
     * @param Illuminate\Http\Request; $request HTTPリクエストオブジェクト
     *
     * @return \Illuminate\Http\JsonResponse 新規ユーザーのデータを含むJSONレスポンス
     */
    public function signout(Request $request)
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json('signouted', 200);
    }
}
