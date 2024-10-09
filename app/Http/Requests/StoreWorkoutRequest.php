<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkoutRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date_format:Y-m-d',
            'exercises' => 'required|array|min:1',
            'exercises.*.name' => 'required|string',
            'exercises.*.type' => 'required|in:time_based,distance_based,time_distance_based,repetition_based',
            'exercises.*.duration' => 'nullable|numeric|min:0|required_if:exercises.*.type,time_based|required_if:exercises.*.type,time_distance_based',
            'exercises.*.distance' => 'nullable|numeric|min:0|required_if:exercises.*.type,distance_based|required_if:exercises.*.type,time_distance_based',
            'exercises.*.sets' => 'nullable|integer|min:1|required_if:exercises.*.type,repetition_based',
            'exercises.*.reps' => 'nullable|integer|min:1|required_if:exercises.*.type,repetition_based',
            'exercises.*.weight' => 'nullable|numeric|min:0',
            'exercises.*.calories' => 'nullable|numeric|min:0',
            'exercises.*.comment' => 'nullable|string',
        ];
    }

    /**
     * Custom messages for validation errors
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The user ID must exist in the users table.',
            'date.required' => 'The date of the workout is required.',
            'date.date_format' => 'The date must be in YYYY-MM-DD format.',
            'exercises.required' => 'At least one exercise must be included.',
            'exercises.array' => 'Exercises must be provided as an array.',
            'exercises.*.name.required' => 'Each exercise must have a name.',
            'exercises.*.type.required' => 'Each exercise must have a type.',
            'exercises.*.type.in' => 'The exercise type must be one of time_based, distance_based, time_distance_based, or repetition_based.',
            'exercises.*.duration.required_if' => 'Duration is required for time-based or time-distance-based exercises.',
            'exercises.*.distance.required_if' => 'Distance is required for distance-based or time-distance-based exercises.',
            'exercises.*.sets.required_if' => 'Sets are required for repetition-based exercises.',
            'exercises.*.reps.required_if' => 'Reps are required for repetition-based exercises.',
            'exercises.*.weight.numeric' => 'Weight must be a number.',
            'exercises.*.calories.numeric' => 'Calories must be a number.',
        ];
    }
}
