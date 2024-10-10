<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'calories',
        'protein',
        'carbs',
        'fats',
    ];

    /**
     * The meals that the food is associated with.
     */
    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'meal_foods')
                    ->withPivot('amount')
                    ->withTimestamps();
    }
}
