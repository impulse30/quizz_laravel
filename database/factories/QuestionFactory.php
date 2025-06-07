<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        return [
            'content' => $this->faker->sentence() . '?',
            'category_id' => Category::factory(), // Assumes CategoryFactory exists and can be used
            'creator_id' => User::factory(),     // Assumes UserFactory exists and can be used
            'difficulty' => $this->faker->randomElement(['easy', 'medium', 'hard']),
        ];
    }
}
