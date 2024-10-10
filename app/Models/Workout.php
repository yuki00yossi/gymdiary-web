<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['workout_id'] = $this->id;  // IDにエイリアスを設定
        return $array;
    }
}
