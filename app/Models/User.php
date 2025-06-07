<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * Represents a user in the application. Users can have roles like 'creator' or 'player'.
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */ // Escaped backslash
    use HasFactory, Notifiable, HasApiTokens; // Standard traits for Laravel user model

    /**
     * The attributes that are mass assignable.
     *
     * 'name' - The user's full name.
     * 'email' - The user's email address (must be unique).
     * 'password' - The user's hashed password.
     * 'role' - The user's role in the system (e.g., 'creator', 'player').
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password', 'role'];

    /**
     * The attributes that should be hidden for serialization.
     * These attributes will not be included in JSON responses by default.
     *
     * 'password' - Hashed password.
     * 'remember_token' - Token for "remember me" functionality.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     * Defines how certain attributes are converted when accessed.
     *
     * 'email_verified_at' - Casts to a Carbon datetime instance.
     * 'password' - Automatically hashes the password when it's set.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Ensures password is automatically hashed
        ];
    }

    /**
     * Get the questions created by this user.
     * Defines a one-to-many relationship with the Question model.
     * A user (creator) can have multiple questions.
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'creator_id');
    }

    /**
     * Get the quizzes played by this user.
     * Defines a one-to-many relationship with the Quiz model.
     * A user (player) can have multiple quiz attempts.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'player_id');
    }
}
