<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions created by the authenticated user.
     *
     * Eager loads associated choices and category for each question.
     * Only returns questions where 'creator_id' matches the authenticated user's ID.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Retrieve questions created by the authenticated user, along with their choices and category
        $questions = Question::with('choices', 'category')
            ->where('creator_id', auth()->id()) // Filter by the current authenticated user
            ->get();

        return response()->json(['data' => $questions]);
    }

    /**
     * Store a newly created question in storage along with its choices.
     *
     * Validates request data for question content, category, difficulty, and choices.
     * Associates the question with the authenticated user as its creator.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate incoming request data for the question and its choices
        $data = $request->validate([
            'content'     => 'required|string', // Question text
            'category_id' => 'required|exists:categories,id', // Category must exist
            'difficulty'  => 'required|in:easy,medium,hard', // Difficulty level
            'choices'     => 'required|array|min:2', // Must have at least two choices
            'choices.*.content'    => 'required|string', // Each choice must have content
            'choices.*.is_correct' => 'required|boolean', // Each choice must specify if it's correct
        ]);

        // Create the question and assign the authenticated user as the creator
        $question = Question::create([
            'content'     => $data['content'],
            'category_id' => $data['category_id'],
            'difficulty'  => $data['difficulty'],
            'creator_id'  => auth()->id(), // Set the creator to the current user
        ]);

        // Create and associate choices with the question
        foreach ($data['choices'] as $choiceData) {
            $question->choices()->create($choiceData);
        }

        // Return the newly created question (with its choices loaded) and a 201 Created status
        return response()->json(['data' => $question->load('choices')], 201);
    }

    /**
     * Display the specified question if owned by the authenticated user.
     *
     * Eager loads choices and category for the question.
     * Returns 403 Unauthorized if the question does not belong to the user.
     *
     * @param  \App\Models\Question  $question The Question model instance (route model binding).
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Question $question)
    {
        // Authorization check: ensure the authenticated user is the creator of the question
        if ($question->creator_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized to view this question.'], 403);
        }

        // Return the specified question, eager loading its choices and category
        return response()->json(['data' => $question->load('choices', 'category')]);
    }

    /**
     * Update the specified question in storage if owned by the authenticated user.
     *
     * Validates request data for content, category, and difficulty.
     * Returns 403 Unauthorized if the question does not belong to the user.
     * Note: This method does not currently support updating choices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question The Question model instance to update.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Question $question)
    {
        // Authorization check: ensure the authenticated user is the creator of the question
        if ($question->creator_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized to update this question.'], 403);
        }

        // Validate incoming request data (fields are optional for update)
        $data = $request->validate([
            'content'     => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'difficulty'  => 'sometimes|required|in:easy,medium,hard',
        ]);

        // Update the question with validated data
        $question->update($data);

        // Return the updated question data
        return response()->json(['data' => $question]);
    }

    /**
     * Remove the specified question from storage if owned by the authenticated user.
     *
     * Returns 403 Unauthorized if the question does not belong to the user.
     * Note: Associated choices will typically be deleted via cascading delete if set up in DB,
     * or should be handled manually if not.
     *
     * @param  \App\Models\Question  $question The Question model instance to delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Question $question)
    {
        // Authorization check: ensure the authenticated user is the creator of the question
        if ($question->creator_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized to delete this question.'], 403);
        }

        // Delete the question (associated choices might be handled by database foreign key constraints)
        $question->delete();

        // Return a success message
        return response()->json(['message' => 'Question deleted successfully']);
    }
}
