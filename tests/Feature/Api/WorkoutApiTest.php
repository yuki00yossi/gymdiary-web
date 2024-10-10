<?php
/**
 * Tests for Workout API.
 */

use App\Models\User;
use App\Models\Workout;
use App\Models\Exercise;
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


/**
 * ---------------------------------------------------
 * Tests for Retrieve API.
 * ---------------------------------------------------
 */
it('can retrieve all workouts for a user', function () {
    $user = User::factory()->create();

    Workout::factory()
        ->count(3)
        ->for($user)
        ->has(Exercise::factory()->count(2))
        ->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/workouts?user_id=' . $user->id);

    $response->assertStatus(200)
             ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'workout_id',
                        'user_id',
                        'date',
                        'exercises' => [
                            '*' => [
                                'exercise_id',
                                'name',
                                'type',
                                'sets',
                                'reps',
                                'weight',
                                'calories',
                                'comment'
                            ]
                        ]
                    ]
                ]
             ]);
});

it('can retrieve workouts filtered by date', function () {
    $user = User::factory()->create();

    Workout::factory()->for($user)->has(Exercise::factory()->count(2))->create(['date' => '2024-10-08']);
    Workout::factory()->for($user)->has(Exercise::factory()->count(2))->create(['date' => '2024-10-09']);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/workouts?user_id=' . $user->id . '&date=2024-10-08');

    $response->assertStatus(200)
             ->assertJsonCount(1, 'data')
             ->assertJsonFragment(['date' => '2024-10-08']);
});

it('can retrieve workouts filtered by exercise type', function () {
    $user = User::factory()->create();

    Workout::factory()->for($user)->has(Exercise::factory()->count(2)->state(['type' => 'repetition_based']))->create();
    Workout::factory()->for($user)->has(Exercise::factory()->count(2)->state(['type' => 'time_based']))->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/workouts?user_id=' . $user->id . '&exercise_type=repetition_based');

    $response->assertStatus(200)
             ->assertJsonFragment(['type' => 'repetition_based']);
});

it('fails if user_id is missing', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/workouts');

    $response->assertStatus(422)
             ->assertJson([
                'message' => 'The user ID is required.',
                'errors' => [
                    'user_id' => ['The user ID is required.']
                ]
             ]);
});

it('fails if date is in an invalid format', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/workouts?user_id=' . $user->id . '&date=2024/10/08'); // Invalid date format

    $response->assertStatus(422)
             ->assertJson([
                'message' => 'The date must be in YYYY-MM-DD format.',
                'errors' => [
                    'date' => ['The date must be in YYYY-MM-DD format.']
                ]
             ]);
});

it('fails if exercise_type is invalid', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/workouts?user_id=' . $user->id . '&exercise_type=invalid_type'); // Invalid exercise type

    $response->assertStatus(422)
             ->assertJson([
                'message' => 'The exercise type must be one of time_based, distance_based, time_distance_based, or repetition_based.',
                'errors' => [
                    'exercise_type' => ['The exercise type must be one of time_based, distance_based, time_distance_based, or repetition_based.']
                ]
             ]);
});

it('returns 404 if no workouts are found', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/workouts?user_id=' . $user->id);

    $response->assertStatus(404)
             ->assertJson([
                'message' => 'No workout records found for the given user.'
             ]);
});


/**
 * ---------------------------------------------------
 * Tests for Update API.
 * ---------------------------------------------------
 */
it('can update a workout with valid data', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exercise = Exercise::factory()->for($workout)->create();

    Sanctum::actingAs($user);

    $response = $this->putJson('/api/workouts/' . $workout->id, [
        'user_id' => $user->id,
        'date' => '2024-10-08',
        'exercises' => [
            [
                'id' => $exercise->id,
                'name' => 'Squat',
                'type' => 'repetition_based',
                'sets' => 4,
                'reps' => 12,
                'weight' => 90,
                'calories' => 200,
                'comment' => 'Updated comment',
            ]
        ]
    ]);

    $response->assertStatus(200)
             ->assertJson([
                'message' => 'Workout updated successfully',
                'data' => [
                    'date' => '2024-10-08',
                    'exercises' => [
                        [
                            'id' => $exercise->id,
                            'name' => 'Squat',
                            'sets' => 4,
                            'reps' => 12,
                            'weight' => 90,
                            'comment' => 'Updated comment',
                        ]
                    ]
                ]
             ]);
});

it('can add a new exercise to a workout', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();

    Sanctum::actingAs($user);

    $response = $this->putJson('/api/workouts/' . $workout->id, [
        'user_id' => $user->id,
        'date' => '2024-10-09',
        'exercises' => [
            [
                'name' => 'Running',
                'type' => 'time_based',
                'duration' => 30,
                'calories' => 300,
                'comment' => 'Great run',
            ]
        ]
    ]);

    $response->assertStatus(200)
             ->assertJson([
                'message' => 'Workout updated successfully',
                'data' => [
                    'date' => '2024-10-09',
                    'exercises' => [
                        [
                            'name' => 'Running',
                            'type' => 'time_based',
                            'duration' => 30,
                            'calories' => 300,
                            'comment' => 'Great run',
                        ]
                    ]
                ]
             ]);
});

it('fails if the user tries to update another user\'s workout', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $workout = Workout::factory()->for($user1)->create();

    Sanctum::actingAs($user2);

    $response = $this->putJson('/api/workouts/' . $workout->id, [
        'user_id' => $user2->id,
        'date' => '2024-10-08',
        'exercises' => [
            [
                'name' => 'Squat',
                'type' => 'repetition_based',
                'sets' => 4,
                'reps' => 12,
                'weight' => 90,
                'calories' => 200,
                'comment' => 'Good session',
            ]
        ]
    ]);

    $response->assertStatus(403)
             ->assertJson([
                'message' => 'This action is unauthorized.'
             ]);
});

it('fails if required fields are missing for update', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();

    Sanctum::actingAs($user);

    $response = $this->putJson('/api/workouts/' . $workout->id, [
        'user_id' => $user->id,
        // Missing date and exercises
    ]);

    $response->assertStatus(422) // Validation error
             ->assertJson([
                'message' => 'The date of the workout is required. (and 1 more error)',
                'errors' => [
                    'date' => ['The date of the workout is required.'],
                    'exercises' => ['At least one exercise must be included.']
                ]
             ]);
});

it('fails if the date format is invalid', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();

    Sanctum::actingAs($user);

    $response = $this->putJson('/api/workouts/' . $workout->id, [
        'user_id' => $user->id,
        'date' => '10-08-2024', // Invalid date format
        'exercises' => [
            [
                'name' => 'Running',
                'type' => 'time_based',
                'duration' => 30,
                'calories' => 300,
                'comment' => 'Great run',
            ]
        ]
    ]);

    $response->assertStatus(422)
             ->assertJson([
                'message' => 'The date must be in YYYY-MM-DD format.',
                'errors' => [
                    'date' => ['The date must be in YYYY-MM-DD format.']
                ]
             ]);
});

it('fails if exercise type is invalid', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();

    Sanctum::actingAs($user);

    $response = $this->putJson('/api/workouts/' . $workout->id, [
        'user_id' => $user->id,
        'date' => '2024-10-08',
        'exercises' => [
            [
                'name' => 'Jumping',
                'type' => 'invalid_type', // Invalid exercise type
                'sets' => 4,
                'reps' => 12,
                'calories' => 150,
            ]
        ]
    ]);

    $response->assertStatus(422)
             ->assertJson([
                'message' => 'The exercise type must be one of time_based, distance_based, time_distance_based, or repetition_based.',
                'errors' => [
                    'exercises.0.type' => ['The exercise type must be one of time_based, distance_based, time_distance_based, or repetition_based.']
                ]
             ]);
});

it('fails if sets and reps are missing for repetition_based exercise', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();

    Sanctum::actingAs($user);

    $response = $this->putJson('/api/workouts/' . $workout->id, [
        'user_id' => $user->id,
        'date' => '2024-10-08',
        'exercises' => [
            [
                'name' => 'Push-up',
                'type' => 'repetition_based',
                'calories' => 100, // Missing sets and reps
            ]
        ]
    ]);

    $response->assertStatus(422)
             ->assertJson([
                'message' => 'Sets are required for repetition-based exercises. (and 1 more error)',
                'errors' => [
                    'exercises.0.sets' => ['Sets are required for repetition-based exercises.'],
                    'exercises.0.reps' => ['Reps are required for repetition-based exercises.']
                ]
             ]);
});
