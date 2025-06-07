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

    public function start(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'count'       => 'sometimes|integer|min:1|max:50',
    ]);

    $count = $request->input('count', 10);

    $questions = Question::with('choices')
        ->where('category_id', $request->category_id)
        ->inRandomOrder()
        ->take($count)
        ->get();

    // Optional: Could create a Quiz record here to mark the start
    // For now, keeping it simple and creating the Quiz record on submit

    return response()->json(['data' => $questions]);
}

    public function submit(Request $request)
    {
        $data = $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.choice_id'   => 'required|exists:choices,id',
        ]);

        $score = 0;
        $userId = Auth::id(); // Get authenticated user's ID
        $now = Carbon::now();

        // Create the Quiz attempt instance
        $quiz = Quiz::create([
            'player_id'  => $userId,
            'score'      => 0, // Initialize score, will be updated after calculating
            'started_at' => $now, // Or from a previous step if implemented
            'ended_at'   => $now,
        ]);

        foreach ($data['answers'] as $answerData) {
            $choice = Choice::find($answerData['choice_id']);

            $isCorrect = false;
            if ($choice && $choice->is_correct) {
                $score++;
                $isCorrect = true;
            }

            Answer::create([
                'quiz_id'     => $quiz->id, // Associate with the created Quiz
                'question_id' => $answerData['question_id'],
                'choice_id'   => $answerData['choice_id'],
                'is_correct'  => $isCorrect,
            ]);
        }

        // Update the score for the quiz
        $quiz->score = $score;
        $quiz->save();

        // $request->user()->increment('score', $score); // Removed this line

        return response()->json([
            'message' => 'Quiz submitted successfully!',
            'data' => [
                'quiz_id' => $quiz->id,
                'score' => $score
            ]
        ]);
    }
}
