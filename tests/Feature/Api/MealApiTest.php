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

    $this->actingAs($user);

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

    $this->actingAs($user);

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

    $this->actingAs($user);

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

    $this->actingAs($user);

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

    $this->actingAs($user);

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

    $this->actingAs($user);

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

    $this->actingAs($user);

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


/**
 * ---------------------------------------------------
 * Tests for Retrieval API.
 * ---------------------------------------------------
 */
it('can retrieve meal history for authenticated user', function () {
    $user = User::factory()->create();
    $meal = Meal::factory()->for($user)->create();
    $food = Food::factory()->create();
    $meal->foods()->attach($food, ['amount' => 100]);

    $this->actingAs($user);

    $response = $this->getJson("/api/meals/{$user->id}");

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'user_id',
                 'meals' => [
                     '*' => [
                         'id',
                         'date',
                         'meal_type',
                         'foods' => [
                             '*' => [
                                 'name',
                                 'calories',
                                 'protein',
                                 'carbs',
                                 'fats'
                             ]
                         ]
                     ]
                 ]
             ]);
});

it('filters meal history by date and meal_type', function () {
    $user = User::factory()->create();
    $meal1 = Meal::factory()->for($user)->create(['date' => '2024-10-08', 'meal_type' => 'lunch']);
    $meal2 = Meal::factory()->for($user)->create(['date' => '2024-10-07', 'meal_type' => 'breakfast']);
    $food = Food::factory()->create();
    $meal1->foods()->attach($food, ['amount' => 150]);
    $meal2->foods()->attach($food, ['amount' => 200]);

    $this->actingAs($user);

    $response = $this->getJson("/api/meals/{$user->id}?startDate=2024-10-07&endDate=2024-10-08&meal_type=lunch");

    $response->assertStatus(200)
             ->assertJsonCount(1, 'meals')
             ->assertJsonFragment(['meal_type' => 'lunch']);
});

it('returns 404 for non-existent user', function () {
    $this->actingAs(User::factory()->create());

    $response = $this->getJson('/api/meals/999');

    $response->assertStatus(404)
             ->assertJson([
                 'message' => 'User not found.'
             ]);
});

it('returns empty meal history for a user with no meals', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->getJson("/api/meals/{$user->id}");

    $response->assertStatus(200)
             ->assertJson(['meals' => []]);
});


/**
 * ---------------------------------------------------
 * Tests for Update API.
 * ---------------------------------------------------
 */
it('can update a meal with new and modified foods', function () {
    $user = User::factory()->create();
    $meal = Meal::factory()->for($user)->create();
    $existingFood = Food::factory()->create();
    $meal->foods()->attach($existingFood->id, ['amount' => 150]);

    $this->actingAs($user);

    $response = $this->putJson("/api/meals/{$meal->id}", [
        'date' => '2024-10-10 08:00:00',
        'meal_type' => 'lunch',
        'foods' => [
            [
                'food_id' => $existingFood->id,
                'amount' => 200,
                'calories' => $existingFood->calories + 50 // Modify the calories
            ],
            [
                'name' => 'Salad',
                'calories' => 100,
                'protein' => 5,
                'carbs' => 10,
                'fats' => 2,
                'amount' => 100,
            ]
        ]
    ]);

    $response->assertStatus(200)
             ->assertJson(['message' => 'Meal updated successfully']);

    // 新しい食品レコードが作成されていることを確認
    $this->assertDatabaseHas('foods', ['calories' => $existingFood->calories + 50]);
    $this->assertDatabaseHas('foods', ['name' => 'Salad']);
    $this->assertDatabaseHas('meal_foods', ['amount' => 200]);
});

/**
 * ---------------------------------------------------
 * Tests for Destroy API.
 * ---------------------------------------------------
 */
it('can delete a meal', function () {
    $user = User::factory()->create();
    $meal = Meal::factory()->for($user)->create();

    $this->actingAs($user);

    $response = $this->deleteJson("/api/meals/{$meal->id}");

    $response->assertStatus(200)
             ->assertJson(['message' => 'Meal deleted successfully']);

    $this->assertDatabaseMissing('meals', ['id' => $meal->id]);
});

it('cannot delete another user\'s meal', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $meal = Meal::factory()->for($user2)->create();

    $this->actingAs($user1);

    $response = $this->deleteJson("/api/meals/{$meal->id}");

    $response->assertStatus(403)
             ->assertJson(['message' => 'You do not have permission to delete this meal']);
});

it('returns 404 for non-existent meal', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->deleteJson('/api/meals/999');

    $response->assertStatus(404)
             ->assertJson(['message' => 'Meal not found']);
});
