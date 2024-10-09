<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkoutRequest;
use App\Models\Workout;


class WorkoutController extends Controller
{

    public function store(StoreWorkoutRequest $request)
    {
        $workout = Workout::create([
            'user_id' => $request->user()->id,
            'date' => $request->date,
        ]);

        foreach ($request->exercises as $exerciseData) {
            $workout->exercises()->create($exerciseData);
        }

        return response()->json(['message' => 'Workout created successfully'], 201);
    }
}
