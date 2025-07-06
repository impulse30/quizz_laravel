<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Retrieve all categories from the database
        $categories = Category::all();
        return response()->json(['data' => $categories]);
    }

    /**
     * Store a newly created category in storage.
     *
     * Validates the incoming request data for 'name' and 'description',
     * creates a new Category model instance, and saves it to the database.
     * Returns the created category data with a 201 HTTP status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $data = $request->validate([
            'name'        => 'required|string|unique:categories,name', // Name is required and must be unique in the categories table
            'description' => 'nullable|string', // Description is optional
        ]);

        // Create a new category with the validated data
        $category = Category::create($data);

        // Return the newly created category with a 201 Created status
        return response()->json(['data' => $category], 201);
    }

    /**
     * Display the specified category.
     *
     * Uses route model binding to inject the Category instance.
     *
     * @param  \App\Models\Category  $category The Category model instance.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Category $category)
    {
        // Return the specified category
        return response()->json(['data' => $category]);
    }

    /**
     * Update the specified category in storage.
     *
     * Validates the incoming request data. The 'name' field is sometimes required
     * and must be unique, ignoring the current category's ID.
     * Updates the category with the validated data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category The Category model instance to update.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Category $category)
    {
        // Validate the incoming request data
        // 'sometimes' means the field is only validated if present in the request
        // Unique rule for 'name' ignores the current category ID to allow updating other fields without changing name,
        // or changing name to something not already taken by another category.
        $data = $request->validate([
            'name'        => 'sometimes|required|string|unique:categories,name,' . $category->id,
            'description' => 'nullable|string', // Description is optional
        ]);

        // Update the category with validated data
        $category->update($data);

        // Return the updated category data
        return response()->json(['data' => $category]);
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  \App\Models\Category  $category The Category model instance to delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {
        // Delete the category
        $category->delete();

        // Return a success message
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
