<?php
/**
 * Tests for Meal API.
 */

use App\Models\User;
use App\Models\Food;
use App\Models\Meal;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * ---------------------------------------------------
 * Tests for Store API.
 * ---------------------------------------------------
 */
it('can create a meal with existing and new foods', function () {
    $user = User::factory()->create();
    $existingFood = Food::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/meals', [
        'date' => '2024-10-10 12:00:00',
        'meal_type' => 'lunch',
        'foods' => [
            [
                'food_id' => $existingFood->id,
                'amount' => 150,
            ],
            [
                'name' => 'New Food',
                'calories' => 200,
                'protein' => 15,
                'carbs' => 30,
                'fats' => 10,
                'amount' => 100,
            ]
        ]
    ]);

    $response->assertStatus(201)
             ->assertJson(['message' => 'Meal created successfully']);

    $this->assertDatabaseHas('meals', ['user_id' => $user->id]);
    $this->assertDatabaseHas('meal_foods', ['amount' => 150, 'food_id' => $existingFood->id]);
    $this->assertDatabaseHas('foods', ['name' => 'New Food']);
});

it('can create a meal with only existing foods', function () {
    $user = User::factory()->create();
    $existingFood = Food::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/meals', [
        'date' => '2024-10-10 12:00:00',
        'meal_type' => 'dinner',
        'foods' => [
            [
                'food_id' => $existingFood->id,
                'amount' => 200,
            ]
        ]
    ]);

    $response->assertStatus(201)
             ->assertJson(['message' => 'Meal created successfully']);

    $this->assertDatabaseHas('meals', ['user_id' => $user->id]);
    $this->assertDatabaseHas('meal_foods', ['amount' => 200, 'food_id' => $existingFood->id]);
});

it('can create a meal with only new foods', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/meals', [
        'date' => '2024-10-10 12:00:00',
        'meal_type' => 'breakfast',
        'foods' => [
            [
                'name' => 'Pancakes',
                'calories' => 250,
                'protein' => 10,
                'carbs' => 50,
                'fats' => 5,
                'amount' => 150,
            ]
        ]
    ]);

    $response->assertStatus(201)
             ->assertJson(['message' => 'Meal created successfully']);

    $this->assertDatabaseHas('meals', ['user_id' => $user->id]);
    $this->assertDatabaseHas('foods', ['name' => 'Pancakes']);
    $this->assertDatabaseHas('meal_foods', ['amount' => 150]);
});

it('fails to create a meal if unauthenticated', function () {
    $response = $this->postJson('/api/meals', []);

    $response->assertStatus(401); // Unauthorized
});

it('fails if required fields are missing', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/meals', [
        'date' => '2024-10-10 12:00:00',
        'foods' => [
            [
                'name' => 'Pancakes',
                'calories' => 250,
                'amount' => 150,
            ]
        ]
    ]);

    $response->assertStatus(422) // Validation error
             ->assertJsonValidationErrors(['meal_type']);
});

it('fails if the date format is invalid', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/meals', [
        'date' => '10-10-2024 12:00:00', // Invalid date format
        'meal_type' => 'breakfast',
        'foods' => [
            [
                'name' => 'Pancakes',
                'calories' => 250,
                'amount' => 150,
            ]
        ]
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['date']);
});

it('fails if calories or amount is negative', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/meals', [
        'date' => '2024-10-10 12:00:00',
        'meal_type' => 'snack',
        'foods' => [
            [
                'name' => 'Cookies',
                'calories' => -100, // Invalid negative value
                'amount' => -50,    // Invalid negative value
            ]
        ]
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['foods.0.calories', 'foods.0.amount']);
});

it('fails if a non-existent food_id is provided', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/meals', [
        'date' => '2024-10-10 12:00:00',
        'meal_type' => 'snack',
        'foods' => [
            [
                'food_id' => 9999, // Non-existent food ID
                'amount' => 100,
            ]
        ]
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['foods.0.food_id']);
});
