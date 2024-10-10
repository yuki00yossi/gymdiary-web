<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Workout;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workout>
 */
class WorkoutFactory extends Factory
{

    protected $model = Workout::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(), // 関連するユーザーを自動生成
            'date' => $this->faker->date(), // ランダムな日付を生成
        ];
    }
}
