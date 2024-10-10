<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetWorkoutRequest;
use App\Http\Requests\StoreWorkoutRequest;
use App\Models\Workout;


class WorkoutController extends Controller
{

    /**
     * Handle the incoming request to retrieve workouts.
     *
     * @param GetWorkoutRequest $request
     * @return JsonResponse
     */
    public function retrieve(GetWorkoutRequest $request)
    {
        $query = Workout::with('exercises')->where('user_id', $request->user_id);

        // オプションで日付によるフィルタリング
        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        // オプションでエクササイズタイプによるフィルタリング
        if ($request->filled('exercise_type')) {
            $query->whereHas('exercises', function ($q) use ($request) {
                $q->where('type', $request->exercise_type);
            });
        }

        $workouts = $query->get();

        if ($workouts->isEmpty()) {
            return response()->json([
                'message' => 'No workout records found for the given user.'
            ], 404);
        }

        return response()->json([
            'message' => 'Workouts retrieved successfully',
            'data' => $workouts,
        ], 200);
    }

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
