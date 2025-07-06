<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Category
 *
 * Represents a category for organizing questions.
 * Each category can have multiple questions.
 */
class Category extends Model
{
    use HasFactory; // Enables the use of factories for seeding and testing.

    /**
     * The attributes that are mass assignable.
     *
     * 'name' - The name of the category (e.g., "Science", "History").
     * 'description' - An optional description of the category.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'description'];

    /**
     * Get the questions associated with this category.
     * Defines a one-to-many relationship with the Question model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
