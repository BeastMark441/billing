<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfrastructureCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InfrastructureCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = InfrastructureCategory::withCount('services')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.infrastructure.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.infrastructure.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:infrastructure_categories',
            'slug' => 'nullable|string|max:255|unique:infrastructure_categories',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        InfrastructureCategory::create($validated);

        return redirect()->route('admin.infrastructure.categories.index')
            ->with('success', 'Категория создана.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InfrastructureCategory $category)
    {
        return view('admin.infrastructure.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InfrastructureCategory $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('infrastructure_categories')->ignore($category->id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('infrastructure_categories')->ignore($category->id)],
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('admin.infrastructure.categories.index')
            ->with('success', 'Категория обновлена.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InfrastructureCategory $category)
    {
        $category->delete();

        return redirect()->route('admin.infrastructure.categories.index')
            ->with('success', 'Категория удалена.');
    }
}
