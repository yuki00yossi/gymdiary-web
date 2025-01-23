<?php
namespace App\Http\Requests\Auth;


use Illuminate\Validation\Rules\Password;
use App\Models\User;


class SignupRequest extends LoginRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['bail', 'required', 'string', 'lowercase', 'max:255', 'unique:'.User::class],
            'email' => ['bail', 'required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'name' => ['bail', 'required', 'string', 'max:255'],
            'password' => [
                'bail',
                'required',
                Password::min(6) // パスワードの最小文字数
                    ->letters() // 英字を含める
                    ->numbers() // 数字を含める
                    ->uncompromised(), // データ漏洩のチェック（これを削除可能）
            ],
        ];
    }
}
