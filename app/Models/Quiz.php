<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Quiz
 *
 * Represents a single quiz attempt by a user (player).
 * It stores the player's score and the start/end times of the quiz.
 */
class Quiz extends Model
{
    use HasFactory; // Enables the use of factories for seeding and testing.

    /**
     * The attributes that are mass assignable.
     *
     * 'player_id' - Foreign key linking to the User model (the player who took the quiz).
     * 'score' - The score achieved by the player in this quiz attempt.
     * 'started_at' - Timestamp indicating when the quiz was started.
     * 'ended_at' - Timestamp indicating when the quiz was completed/submitted.
     *
     * @var array<int, string>
     */
    protected $fillable = ['player_id', 'score', 'started_at', 'ended_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * 'started_at' - Casts to a Carbon datetime instance.
     * 'ended_at' - Casts to a Carbon datetime instance.
     * 'score' - Casts to an integer.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'score' => 'integer',
    ];

    /**
     * Get the player (user) who took this quiz.
     * Defines an inverse one-to-many relationship with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    /**
     * Get all the answers submitted for this quiz attempt.
     * Defines a one-to-many relationship with the Answer model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
