<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Meal;
use App\Models\Food;
use App\Models\MealFood;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;

class MealController extends Controller
{

    /**
     * Retrieve meal history for the authenticated user.
     *
     * @param Request $request
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function retrieve(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }
        // クエリパラメータを使用して日付や食事タイプでフィルタリング
        $mealsQuery = Meal::with('foods')
            ->where('user_id', $userId)
            ->orderBy('date', 'desc');

        if ($request->has('startDate')) {
            $mealsQuery->where('date', '>=', $request->query('startDate'));
        }

        if ($request->has('endDate')) {
            $mealsQuery->where('date', '<=', $request->query('endDate'));
        }

        if ($request->has('meal_type')) {
            $mealsQuery->where('meal_type', $request->query('meal_type'));
        }

        // 食事履歴を取得
        $meals = $mealsQuery->get();

        return response()->json([
            'user_id' => $userId,
            'meals' => $meals
        ], 200);
    }


    /**
     * Store a newly created meal in storage.
     *
     * @param  StoreMealRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreMealRequest $request)
    {
        $user = Auth::user();

        $meal = Meal::create([
            'user_id' => $user->id,
            'date' => $request->date,
            'meal_type' => $request->meal_type,
        ]);

        // 食品ごとの処理
        foreach ($request->foods as $foodData) {
            // food_id が指定されていない場合、新しい食品を作成
            if (!isset($foodData['food_id'])) {
                $food = Food::create([
                    'name' => $foodData['name'],
                    'calories' => $foodData['calories'],
                    'protein' => $foodData['protein'] ?? 0,
                    'carbs' => $foodData['carbs'] ?? 0,
                    'fats' => $foodData['fats'] ?? 0,
                ]);
                $foodId = $food->id;
            } else {
                // 既存の食品IDを使用
                $foodId = $foodData['food_id'];
            }

            MealFood::create([
                'meal_id' => $meal->id,
                'food_id' => $foodId,
                'amount' => $foodData['amount'],
            ]);
        }

        return response()->json(['message' => 'Meal created successfully'], 201);
    }

    /**
     * Update the specified meal in storage.
     *
     * @param  UpdateMealRequest  $request
     * @param  int  $mealId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateMealRequest $request, $mealId)
    {
        // 認証されたユーザーの食事のみ更新可能
        $meal = Meal::where('id', $mealId)->where('user_id', Auth::id())->first();

        if (!$meal) {
            return response()->json(['message' => 'Meal not found'], 404);
        }

        // 食事の基本情報を更新
        $meal->update([
            'date' => $request->date,
            'meal_type' => $request->meal_type,
        ]);

        // 現在の食品データを削除し、新しい食品データに差し替える
        $meal->foods()->detach();

        foreach ($request->foods as $foodData) {
            // 食品情報の変更がある場合は新しい食品を作成
            if (isset($foodData['food_id'])) {
                $existingFood = Food::find($foodData['food_id']);

                // カロリーなどの情報が異なる場合は新しい食品を作成
                if ($existingFood->calories != $foodData['calories'] ||
                    $existingFood->protein != ($foodData['protein'] ?? 0) ||
                    $existingFood->carbs != ($foodData['carbs'] ?? 0) ||
                    $existingFood->fats != ($foodData['fats'] ?? 0)) {

                    // 新しい食品を作成
                    $food = Food::create([
                        'name' => $existingFood->name,
                        'calories' => $foodData['calories'],
                        'protein' => $foodData['protein'] ?? 0,
                        'carbs' => $foodData['carbs'] ?? 0,
                        'fats' => $foodData['fats'] ?? 0,
                    ]);
                } else {
                    // 既存の食品をそのまま使用
                    $food = $existingFood;
                }
            } else {
                // 新しい食品の作成
                $food = Food::create([
                    'name' => $foodData['name'],
                    'calories' => $foodData['calories'],
                    'protein' => $foodData['protein'] ?? 0,
                    'carbs' => $foodData['carbs'] ?? 0,
                    'fats' => $foodData['fats'] ?? 0,
                ]);
            }

            // 食事と食品を関連付け
            MealFood::create([
                'meal_id' => $meal->id,
                'food_id' => $food->id,
                'amount' => $foodData['amount'],
            ]);
        }

        return response()->json(['message' => 'Meal updated successfully'], 200);
    }

    /**
     * Delete the specified meal.
     *
     * @param  int  $mealId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($mealId)
    {
        // 認証されたユーザーの食事のみ削除可能
        $meal = Meal::where('id', $mealId)
                    ->where('user_id', Auth::id())
                    ->first();
        $meal = Meal::find($mealId);

        if (!$meal) {
            return response()->json([
                'message' => 'Meal not found',
            ], 404);
        }

        if ($meal->user_id !== Auth::user()->id) {
            return response()->json(
                ['message' => 'You do not have permission to delete this meal'], 403);
        }

        // 食事を削除
        $meal->delete();

        return response()->json([
            'message' => 'Meal deleted successfully'
        ], 200);
    }
}
