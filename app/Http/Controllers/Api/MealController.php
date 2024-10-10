<?php

namespace App\Http\Controllers\Api;

use App\Models\Meal;
use App\Models\Food;
use App\Models\MealFood;
use App\Http\Requests\StoreMealRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
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
