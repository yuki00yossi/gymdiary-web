<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Exercise;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{

    protected $model = Exercise::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'type' => $this->faker->randomElement(['time_based', 'distance_based', 'time_distance_based', 'repetition_based']),
            'sets' => $this->faker->randomNumber(2),
            'reps' => $this->faker->randomNumber(2),
            'weight' => $this->faker->randomFloat(2, 20, 200),
            'calories' => $this->faker->randomFloat(2, 100, 1000),
            'comment' => $this->faker->sentence(),
        ];
    }
}
