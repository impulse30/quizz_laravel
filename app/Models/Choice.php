<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Choice
 *
 * Represents an answer choice for a question.
 * Each choice belongs to a question and indicates if it's the correct answer.
 */
class Choice extends Model
{
    use HasFactory; // Enables the use of factories for seeding and testing.

    /**
     * The attributes that are mass assignable.
     *
     * 'question_id' - Foreign key linking to the Question model.
     * 'content' - The text content of the choice.
     * 'is_correct' - Boolean indicating if this choice is a correct answer for the question.
     *
     * @var array<int, string>
     */
    protected $fillable = ['question_id', 'content', 'is_correct'];

    /**
     * The attributes that should be cast to native types.
     *
     * 'is_correct' - Casts the 'is_correct' attribute to a boolean.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Get the question to which this choice belongs.
     * Defines an inverse one-to-many relationship with the Question model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
