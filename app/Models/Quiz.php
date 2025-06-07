<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['player_id', 'score', 'started_at', 'ended_at'];

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
