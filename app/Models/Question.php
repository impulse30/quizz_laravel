<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Question
 *
 * Represents a question within a category, created by a user.
 * Each question has multiple choices, one or more of which can be correct.
 */
class Question extends Model
{
    use HasFactory; // Enables the use of factories for seeding and testing.

    /**
     * The attributes that are mass assignable.
     *
     * 'content' - The text of the question itself.
     * 'category_id' - Foreign key linking to the Category model.
     * 'creator_id' - Foreign key linking to the User model (the creator of the question).
     * 'difficulty' - The difficulty level of the question (e.g., 'easy', 'medium', 'hard').
     *
     * @var array<int, string>
     */
    protected $fillable = ['content', 'category_id', 'creator_id', 'difficulty'];

    /**
     * Get the category to which this question belongs.
     * Defines an inverse one-to-many relationship with the Category model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user (creator) who created this question.
     * Defines an inverse one-to-many relationship with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the choices available for this question.
     * Defines a one-to-many relationship with the Choice model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function choices(): HasMany
    {
        return $this->hasMany(Choice::class);
    }
}
