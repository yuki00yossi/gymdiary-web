<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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

    /** 直近７日間の体重情報を取得する */
    public function weight_daily() {
        $dates = collect();
        for ($i = 0; $i < 7; $i++) {
            $dates->push(Carbon::now()->subDays($i)->startOfDay());
        }

        $weights = Weight::where('user_id', $this->id)
            ->whereBetween('date', [$dates->last()->toDateString(), $dates->first()->toDateString()])
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->date)->format('Y-m-d'));
        // 日付ごとにデータをマッピングし、存在しない日付には null を設定し、すべて UTC で返却
        $result = $dates->map(function ($date) use ($weights) {
            $utcDate = $date->copy()->setTimezone('UTC')->toISOString(); // UTCのISO8601形式に変換

            if (isset($weights[$date->toDateString()])) {
                return [
                    'date' => $utcDate,  // UTC形式
                    'weight' => $weights[$date->toDateString()]->weight,
                ];
            }
            return [
                'date' => $utcDate,  // UTC形式
                'weight' => null,
            ];
        })->reverse();

        return array_values($result->toArray());
    }

    /**
     * 直近7週間の平均体重情報を取得するメソッド。
     *
     * 各週ごとの開始日（週の月曜日）およびその週の平均体重を計算して返します。
     * データは最新の週から順に取得され、各週の情報が最大7件含まれます。
     *
     * 結果に含まれる情報:
     * - 平均体重（average_weight）
     * - 週の開始日（月曜日を基準とする日付: week_start_date）
     *
     * 処理の流れ:
     * - 月曜日を週の始まりとし、週ごとに体重データをグループ化。
     * - 各週の体重データを平均し、直近7週間分の結果を取得します。
     *
     * @return \Illuminate\Support\Collection 週ごとの平均体重情報（最大7件）
     */
    public function weight_weekly() {
        $weeks = collect();
        for ($i = 0; $i < 7; $i++) {
            $weeks->push(Carbon::now()->startOfWeek(Carbon::MONDAY)->subWeeks($i)->toDateString());
        }

        // 週ごとの平均体重データを取得
        $weights = Weight::select(
            DB::raw('AVG(weight) as average_weight'),
            DB::raw('DATE_FORMAT(date - INTERVAL WEEKDAY(date) DAY, "%Y-%m-%d") as week_start_date')
        )
            ->where('user_id', $this->id)
            ->groupBy('week_start_date')
            ->orderBy('week_start_date', 'desc')
            ->get()
            ->keyBy('week_start_date');

        // 各週の日付ごとにデータをマッピングし、存在しない週はnullを設定し、UTC形式で返却
        $result = $weeks->map(function ($weekStart) use ($weights) {
            $utcDate = Carbon::parse($weekStart)->setTimezone('UTC')->toISOString();

            if (isset($weights[$weekStart])) {
                return [
                    'date' => $utcDate,  // UTC形式
                    'weight' => round($weights[$weekStart]->average_weight, 2),
                ];
            }

            return [
                'date' => $utcDate,  // UTC形式
                'weight' => null,
            ];
        })->reverse()->toArray();

        return array_values($result);
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }
}
