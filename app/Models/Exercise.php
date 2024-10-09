<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_id', 'name', 'type', 'duration', 'distance', 'sets', 'reps', 'weight', 'calories', 'comment',
    ];

    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }
}
