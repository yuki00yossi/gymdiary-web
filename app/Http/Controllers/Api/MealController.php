<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Meal;
use App\Models\Food;
use App\Models\MealFood;
use App\Http\Requests\StoreMealRequest;
use App\Http\Controllers\Controller;

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
}
