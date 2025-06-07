<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Choice;
use App\Models\Question;
use App\Models\Quiz; // Added import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Added import
use Carbon\Carbon; // Added import

class QuizController extends Controller
{
    /**
     * Start a new quiz by fetching questions for a given category.
     *
     * Validates the category ID and an optional count for the number of questions.
     * Retrieves questions in random order, eager loading their choices.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function start(Request $request)
    {
        // Validate the incoming request for category_id and optional count
        $request->validate([
            'category_id' => 'required|exists:categories,id', // Category must exist
            'count'       => 'sometimes|integer|min:1|max:50',  // If provided, count must be between 1 and 50
        ]);

        // Get the number of questions, default to 10 if not provided
        $count = $request->input('count', 10);

        // Fetch questions from the specified category
        $questions = Question::with('choices') // Eager load choices to prevent N+1 query problem
            ->where('category_id', $request->category_id)
            ->inRandomOrder() // Present questions in a random order
            ->take($count)    // Limit the number of questions
            ->get();

        // Optional: Could create a Quiz record here to mark the start of the attempt.
        // For now, the Quiz record is created upon submission of answers.

        return response()->json(['data' => $questions]);
    }

    /**
     * Submit answers for a quiz, calculate the score, and record the quiz attempt.
     *
     * Validates the submitted answers. Creates a Quiz record for the attempt,
     * records each answer, calculates the score, and updates the Quiz record.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submit(Request $request)
    {
        // Validate the incoming answers payload
        $data = $request->validate([
            'answers'               => 'required|array|min:1', // Must be an array of answers, at least one
            'answers.*.question_id' => 'required|exists:questions,id', // Each answer must link to an existing question
            'answers.*.choice_id'   => 'required|exists:choices,id',   // Each answer must link to an existing choice
        ]);

        $score = 0;
        $userId = Auth::id(); // Get the ID of the authenticated user (player)
        $now = Carbon::now(); // Get the current timestamp

        // Create the Quiz attempt instance
        // This records that a user has attempted a quiz at a specific time.
        $quiz = Quiz::create([
            'player_id'  => $userId,
            'score'      => 0,     // Initialize score, will be updated after calculating all answers
            'started_at' => $now, // Could be set from a previous 'start' step if implemented
            'ended_at'   => $now,   // Marks the submission time as the end time
        ]);

        // Process each submitted answer
        foreach ($data['answers'] as $answerData) {
            $choice = Choice::find($answerData['choice_id']); // Find the chosen choice

            $isCorrect = false;
            // Check if the choice exists and is marked as correct
            if ($choice && $choice->is_correct) {
                $score++; // Increment score for correct answer
                $isCorrect = true;
            }

            // Create an Answer record for this question and choice
            Answer::create([
                'quiz_id'     => $quiz->id, // Link the answer to the created Quiz attempt
                'question_id' => $answerData['question_id'],
                'choice_id'   => $answerData['choice_id'],
                'is_correct'  => $isCorrect, // Store whether this specific answer was correct
            ]);
        }

        // After processing all answers, update the score for the quiz attempt
        $quiz->score = $score;
        $quiz->save();

        // The line below for incrementing a global user score has been intentionally removed
        // $request->user()->increment('score', $score);

        // Return a success message along with the quiz ID and the calculated score
        return response()->json([
            'message' => 'Quiz submitted successfully!',
            'data' => [
                'quiz_id' => $quiz->id,
                'score'   => $score
            ]
        ]);
    }
}
