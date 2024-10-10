<?php

namespace Database\Factories;

use App\Models\Food;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Food::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'calories' => $this->faker->randomFloat(2, 50, 500),  // カロリー 50 ~ 500 のランダム値
            'protein' => $this->faker->randomFloat(2, 0, 50),     // タンパク質 0 ~ 50g のランダム値
            'carbs' => $this->faker->randomFloat(2, 0, 100),      // 炭水化物 0 ~ 100g のランダム値
            'fats' => $this->faker->randomFloat(2, 0, 50),        // 脂質 0 ~ 50g のランダム値
        ];
    }
}
