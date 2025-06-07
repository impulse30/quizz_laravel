<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Added import
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory; // Added trait
    protected $fillable = ['question_id', 'content', 'is_correct'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
