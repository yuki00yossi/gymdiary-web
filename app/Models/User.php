<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /** @var int メアド検証コードの有効期限（分） */
    const MAIL_VERIFY_CODE_EXPIRE_TIME = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'name',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function generateMailVerificationCode()
    {
        $this->mail_verification_code = random_int(100000, 999999);
        $this->mail_verification_code_expires_at = now()->addMinutes(self::MAIL_VERIFY_CODE_EXPIRE_TIME);
        $this->save();
    }

    public function isMailVerificationCodeValid($code)
    {
        return $this->mail_verification_code === $code &&
            $this->mail_verification_code_expires_at &&
            $this->mail_verification_code_expires_at->isFuture();
    }

    // メール確認済みかどうかを判定
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    // メールを確認済みにする
    public function markEmailAsVerified()
    {
        $this->email_verified_at = now();
        $this->save();
    }

    // 認証に使うメールアドレス
    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mail_verification_code_expires_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }
}
