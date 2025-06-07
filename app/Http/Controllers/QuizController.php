<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Choice;
use App\Models\Question;
use Illuminate\Http\Request;

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

        foreach ($data['answers'] as $answerData) {
            $choice = Choice::find($answerData['choice_id']);

            if ($choice->is_correct) {
                $score++;
            }

            Answer::create([
                'user_id'     => $request->user()->id,
                'question_id' => $answerData['question_id'],
                'choice_id'   => $answerData['choice_id'],
                'is_correct'  => $choice->is_correct,
            ]);
        }

        // Facultatif : stocker un score global par session
        $request->user()->increment('score', $score);

        return response()->json(['data' => ['score' => $score]]);
    }


}
