<?php
/**
 * Tests for Workout API.
 */

use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * ---------------------------------------------------
 * Tests for Store API.
 * ---------------------------------------------------
 */
it('can successfully create a workout with valid data', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/workouts', [
        'user_id' => $user->id,
        'date' => '2024-10-08',
        'exercises' => [
            [
                'name' => 'Bench Press',
                'type' => 'repetition_based',
                'sets' => 3,
                'reps' => 10,
                'weight' => 80,
                'calories' => 150,
                'comment' => 'Felt strong',
            ],
        ],
    ]);

    $response->assertStatus(201)
             ->assertJson(['message' => 'Workout created successfully']);
});

it('fails if required fields are missing', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/workouts', [
        'user_id' => $user->id,
        'date' => '2024-10-08',
        // Missing exercises array
    ]);

    $response->assertStatus(422) // 422 Unprocessable Entity is used for validation errors
             ->assertJson([
                'message' => 'At least one exercise must be included.',
                'errors' => [
                    'exercises' => ['At least one exercise must be included.']
                ]
             ]);
});

it('fails if invalid data types are provided', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/workouts', [
        'user_id' => $user->id,
        'date' => 'invalid-date', // Invalid date format
        'exercises' => [
            [
                'name' => 'Bench Press',
                'type' => 'repetition_based',
                'sets' => 'three',  // Invalid sets data type
                'reps' => 10,
                'weight' => 80,
                'calories' => 150,
                'comment' => 'Felt strong',
            ],
        ],
    ]);

    $response->assertStatus(422) // 422 for validation errors
             ->assertJson([
                'message' => 'The date must be in YYYY-MM-DD format. (and 1 more error)',
                'errors' => [
                    'date' => ['The date must be in YYYY-MM-DD format.'],
                    'exercises.0.sets' => ['The exercises.0.sets field must be an integer.']
                ]
             ]);
});

it('can create a workout with optional fields missing', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/workouts', [
        'user_id' => $user->id,
        'date' => '2024-10-08',
        'exercises' => [
            [
                'name' => 'Squat',
                'type' => 'repetition_based',
                'sets' => 4,
                'reps' => 12,
                // Missing weight, calories, and comment (all optional)
            ],
        ],
    ]);

    $response->assertStatus(201)
             ->assertJson(['message' => 'Workout created successfully']);
});

it('fails if user_id does not exist', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/workouts', [
        'user_id' => 9999,  // Non-existent user ID
        'date' => '2024-10-08',
        'exercises' => [
            [
                'name' => 'Bench Press',
                'type' => 'repetition_based',
                'sets' => 3,
                'reps' => 10,
                'weight' => 80,
                'calories' => 150,
                'comment' => 'Felt strong',
            ],
        ],
    ]);

    $response->assertStatus(422)
             ->assertJson([
                'message' => 'The user ID must exist in the users table.',
                'errors' => [
                    'user_id' => ['The user ID must exist in the users table.']
                ]
             ]);
});

it('fails if the user is not authenticated', function () {
    $response = $this->postJson('/api/workouts', [
        'user_id' => 1,  // Dummy data for unauthenticated request
        'date' => '2024-10-08',
        'exercises' => [
            [
                'name' => 'Bench Press',
                'type' => 'repetition_based',
                'sets' => 3,
                'reps' => 10,
                'weight' => 80,
                'calories' => 150,
                'comment' => 'Felt strong',
            ],
        ],
    ]);

    $response->assertStatus(401);
});

it('can create a workout with a time_based exercise', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/workouts', [
        'user_id' => $user->id,
        'date' => '2024-10-08',
        'exercises' => [
            [
                'name' => 'Running',
                'type' => 'time_based',
                'duration' => 30,  // 30 minutes
                'calories' => 300,
                'comment' => 'Great run',
            ],
        ],
    ]);

    $response->assertStatus(201)
             ->assertJson(['message' => 'Workout created successfully']);

    $this->assertDatabaseHas('exercises', [
        'name' => 'Running',
        'duration' => 30,
        'calories' => 300,
        'comment' => 'Great run',
        'type' => 'time_based',
    ]);
});

it('can create a workout with a distance_based exercise', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/workouts', [
        'user_id' => $user->id,
        'date' => '2024-10-08',
        'exercises' => [
            [
                'name' => 'Cycling',
                'type' => 'distance_based',
                'distance' => 15,  // 15 km
                'calories' => 500,
                'comment' => 'Intense cycling',
            ],
        ],
    ]);

    $response->assertStatus(201)
             ->assertJson(['message' => 'Workout created successfully']);

    $this->assertDatabaseHas('exercises', [
        'name' => 'Cycling',
        'distance' => 15,
        'calories' => 500,
        'comment' => 'Intense cycling',
        'type' => 'distance_based',
    ]);
});

it('can create a workout with a time_distance_based exercise', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/workouts', [
        'user_id' => $user->id,
        'date' => '2024-10-08',
        'exercises' => [
            [
                'name' => 'Triathlon',
                'type' => 'time_distance_based',
                'duration' => 120,  // 120 minutes
                'distance' => 20,   // 20 km
                'calories' => 1000,
                'comment' => 'Long endurance training',
            ],
        ],
    ]);

    $response->assertStatus(201)
             ->assertJson(['message' => 'Workout created successfully']);

    $this->assertDatabaseHas('exercises', [
        'name' => 'Triathlon',
        'duration' => 120,
        'distance' => 20,
        'calories' => 1000,
        'comment' => 'Long endurance training',
        'type' => 'time_distance_based',
    ]);
});

it('can create a workout with a repetition_based exercise', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/workouts', [
        'user_id' => $user->id,
        'date' => '2024-10-08',
        'exercises' => [
            [
                'name' => 'Push-ups',
                'type' => 'repetition_based',
                'sets' => 4,
                'reps' => 20,
                'weight' => null,  // No weight for bodyweight exercises
                'calories' => 200,
                'comment' => 'Good push-ups session',
            ],
        ],
    ]);

    $response->assertStatus(201)
             ->assertJson(['message' => 'Workout created successfully']);

    $this->assertDatabaseHas('exercises', [
        'name' => 'Push-ups',
        'sets' => 4,
        'reps' => 20,
        'calories' => 200,
        'comment' => 'Good push-ups session',
        'type' => 'repetition_based',
    ]);
});