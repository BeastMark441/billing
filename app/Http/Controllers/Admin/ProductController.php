<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::with(['nodes','category'])->get();
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:game,vps',
            'category_id' => 'required|exists:categories,id',
            'price_monthly' => 'required|numeric',
            'resources' => 'required|array',
            'resources.cpu' => 'required|integer',
            'resources.ram' => 'required|integer',
            'resources.disk' => 'required|integer',
            'resources.ports' => 'required|integer',
            'is_active' => 'boolean',
            'is_hidden' => 'boolean',
            'nodes' => 'array',
            'nodes.*' => 'exists:nodes,id'
        ];
        if (config('services.pterodactyl.is_pelican')) {
            $rules['resources.egg_id'] = 'required|integer';
        } else {
            $rules['resources.egg_id'] = 'required|integer';
            $rules['resources.nest_id'] = 'required|integer';
        }
        $validated = $request->validate($rules);

        $product = Product::create($validated);
        
        if (isset($validated['nodes'])) {
            $product->nodes()->sync($validated['nodes']);
        }

        return response()->json($product->load('nodes'), 201);
    }

    public function destroy(Product $product)
    {
        $product->nodes()->detach();
        $product->delete();
        return response()->noContent();
    }


    public function show(Product $product)
    {
        return $product->load('nodes');
    }

    public function update(Request $request, Product $product)
    {
        $rules = [
            'name' => 'string',
            'description' => 'nullable|string',
            'type' => 'in:game,vps',
            'category_id' => 'exists:categories,id',
            'price_monthly' => 'numeric',
            'resources' => 'array',
            'is_active' => 'boolean',
            'is_hidden' => 'boolean',
            'nodes' => 'array',
            'nodes.*' => 'exists:nodes,id'
        ];
        if (config('services.pterodactyl.is_pelican')) {
            $rules['resources.egg_id'] = 'integer';
        } else {
            $rules['resources.egg_id'] = 'integer';
            $rules['resources.nest_id'] = 'integer';
        }
        $validated = $request->validate($rules);

        $product->update($validated);

        if (isset($validated['nodes'])) {
            $product->nodes()->sync($validated['nodes']);
        }

        return $product->load(['nodes','category']);
    }

}
