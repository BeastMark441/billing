<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfrastructureCategory;
use App\Models\InfrastructureSubcategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InfrastructureSubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InfrastructureSubcategory::with('category')
            ->orderBy('infrastructure_category_id')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($request->filled('infrastructure_category_id')) {
            $query->where('infrastructure_category_id', $request->infrastructure_category_id);
        }

        $subcategories = $query->paginate(20);

        return view('admin.infrastructure.subcategories.index', compact('subcategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = InfrastructureCategory::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.infrastructure.subcategories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'infrastructure_category_id' => 'required|exists:infrastructure_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:infrastructure_subcategories',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        InfrastructureSubcategory::create($validated);

        return redirect()->route('admin.infrastructure.subcategories.index')
            ->with('success', 'Подкатегория создана.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InfrastructureSubcategory $subcategory)
    {
        $categories = InfrastructureCategory::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.infrastructure.subcategories.edit', compact('subcategory', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InfrastructureSubcategory $subcategory)
    {
        $validated = $request->validate([
            'infrastructure_category_id' => 'required|exists:infrastructure_categories,id',
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('infrastructure_subcategories')->ignore($subcategory->id)],
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $subcategory->update($validated);

        return redirect()->route('admin.infrastructure.subcategories.index')
            ->with('success', 'Подкатегория обновлена.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InfrastructureSubcategory $subcategory)
    {
        $subcategory->delete();

        return redirect()->route('admin.infrastructure.subcategories.index')
            ->with('success', 'Подкатегория удалена.');
    }
}
