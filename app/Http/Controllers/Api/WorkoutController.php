<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetWorkoutRequest;
use App\Http\Requests\StoreWorkoutRequest;
use App\Http\Requests\UpdateWorkoutRequest;
use App\Models\Workout;
use App\Models\Exercise;


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

    /**
     * Update a workout and its exercises.
     *
     * @param UpdateWorkoutRequest $request
     * @param int $workout_id
     * @return JsonResponse
     */
    public function update(UpdateWorkoutRequest $request, $workout_id)
    {
        $workout = Workout::findOrFail($workout_id);

        $workout->update([
            'date' => $request->date,
        ]);

        // エクササイズの更新・追加処理
        foreach ($request->exercises as $exerciseData) {
            if (isset($exerciseData['id'])) {
                // 既存のエクササイズを更新
                $exercise = Exercise::find($exerciseData['id']);
                if ($exercise) {
                    $exercise->update($exerciseData);
                }
            } else {
                // 新規エクササイズを追加
                $workout->exercises()->create($exerciseData);
            }
        }

        return response()->json([
            'message' => 'Workout updated successfully',
            'data' => $workout->load('exercises'),
        ], 200);
    }

    /**
     * Delete a workout.
     *
     * @param int $workout_id
     * @return JsonResponse
     */
    public function destroy(Request $request, $workout_id)
    {
        $workout = Workout::find($workout_id);

        if (!$workout) {
            return response()->json(['message' => 'Workout not found.'], 404);
        }

        // 認証ユーザーが所有者であることを確認
        if ($workout->user_id !== Auth::id()) {
            return response()->json(['message' => 'You do not have permission to delete this workout.'], 403);
        }


        // ワークアウトと関連するエクササイズを削除
        $workout->exercises()->delete();
        $workout->delete();

        return response()->json(['message' => 'Workout deleted successfully'], 200);
    }
}
