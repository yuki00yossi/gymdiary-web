<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'date',
        'meal_type',
    ];

    /**
     * The user that the meal belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The foods associated with the meal.
     */
    public function foods()
    {
        return $this->belongsToMany(Food::class, 'meal_foods')
                    ->withPivot('amount')
                    ->withTimestamps();
    }
}
