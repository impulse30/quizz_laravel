<?php

namespace Database\Factories;

use App\Models\Choice;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChoiceFactory extends Factory
{
    protected $model = Choice::class;

    public function definition()
    {
        return [
            'question_id' => Question::factory(), // Assumes QuestionFactory exists
            'content' => $this->faker->words(3, true), // Generate a few words for choice content
            'is_correct' => $this->faker->boolean(25), // Default to 25% chance of being correct
        ];
    }
}
