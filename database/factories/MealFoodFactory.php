<?php

namespace Database\Factories;

use App\Models\Meal;
use App\Models\Food;
use App\Models\MealFood;
use Illuminate\Database\Eloquent\Factories\Factory;

class MealFoodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MealFood::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'meal_id' => Meal::factory(),
            'food_id' => Food::factory(),
            'amount' => $this->faker->randomFloat(2, 50, 500),  // 食品の量 50 ~ 500g のランダム値
        ];
    }
}
