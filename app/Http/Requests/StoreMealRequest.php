<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date_format:Y-m-d H:i:s',
            'meal_type' => 'required|string',
            'foods' => 'required|array|min:1',
            'foods.*.food_id' => 'nullable|exists:foods,id',
            'foods.*.name' => 'required_without:foods.*.food_id|string',
            'foods.*.calories' => 'required_without:foods.*.food_id|numeric|min:0',
            'foods.*.protein' => 'nullable|numeric|min:0',
            'foods.*.carbs' => 'nullable|numeric|min:0',
            'foods.*.fats' => 'nullable|numeric|min:0',
            'foods.*.amount' => 'required|numeric|min:0',
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'date.required' => 'The date of the meal is required.',
            'date.date_format' => 'The date must be in the format YYYY-MM-DD HH:MM:SS.',
            'meal_type.required' => 'The type of meal is required.',
            'foods.required' => 'At least one food item must be included.',
            'foods.*.food_id.exists' => 'The selected food item does not exist.',
            'foods.*.name.required_without' => 'The food name is required when no food ID is provided.',
            'foods.*.calories.required_without' => 'Calories are required when no food ID is provided.',
            'foods.*.calories.numeric' => 'Calories must be a number.',
            'foods.*.amount.required' => 'The amount of food is required.',
            'foods.*.amount.numeric' => 'The amount of food must be a number.',
        ];
    }
}
