<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetWorkoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',  // user_idは必須かつ存在するユーザー
            'date' => 'nullable|date_format:Y-m-d',  // 日付はオプション、YYYY-MM-DD形式であること
            'exercise_type' => 'nullable|in:time_based,distance_based,time_distance_based,repetition_based', // 種類は指定された範囲内
        ];
    }

    /**
     * Custom error messages for validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The user ID must exist in the users table.',
            'date.date_format' => 'The date must be in YYYY-MM-DD format.',
            'exercise_type.in' => 'The exercise type must be one of time_based, distance_based, time_distance_based, or repetition_based.',
        ];
    }
}
