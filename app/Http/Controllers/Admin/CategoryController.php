<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::withCount('products')->orderBy('id', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'is_visible' => 'boolean',
        ]);

        return Category::create($validated);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'is_visible' => 'boolean',
        ]);

        $category->update($validated);
        return $category;
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->noContent();
    }

    public function setVisibility(Request $request, Category $category)
    {
        $validated = $request->validate([
            'is_visible' => 'required|boolean',
        ]);

        $category->update(['is_visible' => $validated['is_visible']]);
        return $category;
    }

    public function setProductsVisibility(Request $request, Category $category)
    {
        $validated = $request->validate([
            'is_hidden' => 'required|boolean',
        ]);

        $category->products()->update(['is_hidden' => $validated['is_hidden']]);
        return response()->json(['status' => 'ok']);
    }
}
