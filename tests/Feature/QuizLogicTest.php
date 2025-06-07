<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Question;
use App\Models\Choice;
use App\Models\Quiz;
use App\Models\Answer;

class QuizLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_quiz_and_score_is_recorded()
    {
        // 1. Create a user
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. Create a category
        $category = Category::factory()->create();

        // 3. Create questions with choices
        $question1 = Question::factory()->create(['category_id' => $category->id]);
        $choice1_correct = Choice::factory()->create(['question_id' => $question1->id, 'is_correct' => true]);
        $choice1_incorrect = Choice::factory()->create(['question_id' => $question1->id, 'is_correct' => false]);

        $question2 = Question::factory()->create(['category_id' => $category->id]);
        $choice2_incorrect = Choice::factory()->create(['question_id' => $question2->id, 'is_correct' => false]);
        $choice2_correct = Choice::factory()->create(['question_id' => $question2->id, 'is_correct' => true]);

        $question3 = Question::factory()->create(['category_id' => $category->id]);
        $choice3_correct = Choice::factory()->create(['question_id' => $question3->id, 'is_correct' => true]);
        $choice3_incorrect = Choice::factory()->create(['question_id' => $question3->id, 'is_correct' => false]);

        // 4. Simulate quiz submission
        $answersPayload = [
            'answers' => [
                ['question_id' => $question1->id, 'choice_id' => $choice1_correct->id], // Correct
                ['question_id' => $question2->id, 'choice_id' => $choice2_correct->id], // Correct
                ['question_id' => $question3->id, 'choice_id' => $choice3_incorrect->id], // Incorrect
            ]
        ];

        $response = $this->postJson('/api/quiz/submit', $answersPayload);

        // Assertions
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Quiz submitted successfully!',
                     'data' => [
                         'score' => 2 // Expected score: 2 correct answers
                     ]
                 ]);

        $quizId = $response->json('data.quiz_id');
        $this->assertNotNull($quizId, "Quiz ID should not be null.");

        // 5. Assert Quiz record is created
        $this->assertDatabaseHas('quizzes', [
            'id' => $quizId,
            'player_id' => $user->id,
            'score' => 2,
        ]);

        $quiz = Quiz::find($quizId);
        $this->assertNotNull($quiz->started_at, "Quiz started_at should not be null.");
        $this->assertNotNull($quiz->ended_at, "Quiz ended_at should not be null.");


        // 6. Assert Answer records are created and linked
        $this->assertDatabaseHas('answers', [
            'quiz_id' => $quizId,
            'question_id' => $question1->id,
            'choice_id' => $choice1_correct->id,
            'is_correct' => true,
        ]);
        $this->assertDatabaseHas('answers', [
            'quiz_id' => $quizId,
            'question_id' => $question2->id,
            'choice_id' => $choice2_correct->id,
            'is_correct' => true,
        ]);
        $this->assertDatabaseHas('answers', [
            'quiz_id' => $quizId,
            'question_id' => $question3->id,
            'choice_id' => $choice3_incorrect->id,
            'is_correct' => false,
        ]);

        // Ensure the correct number of answers were saved for this quiz
        $this->assertEquals(3, Answer::where('quiz_id', $quizId)->count());
    }
}
