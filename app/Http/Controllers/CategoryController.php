<?php

namespace App\Http\Controllers;

use App\Models\tm_category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = tm_category::where('is_delete', 0)->get();
        return response()->json($categories);
    }

    public function show($id)
    {
        $category = tm_category::find($id);
        if (!$category) {
            return response()->json(['message' => 'category not found'], 404);
        }
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_category' => 'required|string|max:255|unique:tm_category,name_category',
        ], [
            'name_category.unique' => 'name categories sudah ada.',
        ]);

        $category = tm_category::create($validated);
        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = tm_category::find($id);
        if (!$category) {
            return response()->json(['message' => 'category not found'], 404);
        }
        $validated = $request->validate([
            'name_category' => 'required|string|max:255|unique:tm_category,name_category,' . $category->id,
        ], [
            'name_category.unique' => 'name categories sudah ada.',
        ]);

        $category->update($validated);

        $category->slug = null;
        $category->save();
        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = tm_category::find($id);
        if (!$category) {
            return response()->json(['message' => 'category not found'], 404);
        }

        // $category->delete();
        $category->is_delete = 1;
        $category->save();
        return response()->json(['message' => 'category deleted successfully']);
    }
}
