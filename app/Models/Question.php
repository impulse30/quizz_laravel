<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Added import
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory; // Added trait
    protected $fillable = ['content', 'category_id', 'creator_id', 'difficulty'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }
}
