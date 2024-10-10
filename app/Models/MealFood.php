<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealFood extends Model
{
    use HasFactory;

    // 正しいテーブル名を指定
    protected $table = 'meal_foods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'meal_id',
        'food_id',
        'amount',
    ];

    /**
     * The meal that the food belongs to.
     */
    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }

    /**
     * The food associated with the meal.
     */
    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
