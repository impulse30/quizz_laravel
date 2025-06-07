<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('choices', 'category')->where('creator_id', auth()->id())->get();

        return response()->json(['data' => $questions]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content'     => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'difficulty'  => 'required|in:easy,medium,hard',
            'choices'     => 'required|array|min:2',
            'choices.*.content'    => 'required|string',
            'choices.*.is_correct' => 'required|boolean',
        ]);

        $question = Question::create([
            'content'     => $data['content'],
            'category_id' => $data['category_id'],
            'difficulty'  => $data['difficulty'],
            'creator_id'  => auth()->id(),
        ]);

        foreach ($data['choices'] as $choice) {
            $question->choices()->create($choice);
        }

        return response()->json(['data' => $question->load('choices')], 201);
    }

    public function show(Question $question)
    {
        if ($question->creator_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['data' => $question->load('choices', 'category')]);
    }

    public function update(Request $request, Question $question)
    {
        if ($question->creator_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'content'     => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'difficulty'  => 'sometimes|required|in:easy,medium,hard',
        ]);

        $question->update($data);

        return response()->json(['data' => $question]);
    }

    public function destroy(Question $question)
    {
        if ($question->creator_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $question->delete();

        return response()->json(['message' => 'Question deleted']);
    }
}
