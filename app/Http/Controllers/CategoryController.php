<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Category::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($data);

        return response()->json(['data' => $category], 201);
    }

    public function show(Category $category)
    {
        return response()->json(['data' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => 'sometimes|required|string|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($data);

        return response()->json(['data' => $category]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }
}
