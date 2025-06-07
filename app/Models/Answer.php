<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Answer
 *
 * Represents a specific answer given by a player for a question within a quiz attempt.
 * It links a quiz, a question, and the chosen choice, and records if the choice was correct.
 */
class Answer extends Model
{
    use HasFactory; // Enables the use of factories for seeding and testing.

    /**
     * The attributes that are mass assignable.
     *
     * 'quiz_id' - Foreign key linking to the Quiz model (the specific quiz attempt).
     * 'question_id' - Foreign key linking to the Question model.
     * 'choice_id' - Foreign key linking to the Choice model (the choice selected by the player).
     * 'is_correct' - Boolean indicating if the chosen choice was correct for the question.
     *
     * @var array<int, string>
     */
    protected $fillable = ['quiz_id', 'question_id', 'choice_id', 'is_correct'];

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
     * Get the quiz attempt to which this answer belongs.
     * Defines an inverse one-to-many relationship with the Quiz model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the question to which this answer pertains.
     * Defines an inverse one-to-many relationship with the Question model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the choice that was selected for this answer.
     * Defines an inverse one-to-many relationship with the Choice model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function choice(): BelongsTo
    {
        return $this->belongsTo(Choice::class);
    }
}
