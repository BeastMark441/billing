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
            'resources.databases' => 'nullable|integer|min:0',
            'resources.backups' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_hidden' => 'boolean',
            'nodes' => 'array',
            'nodes.*' => 'exists:nodes,id'
        ];
        // Normalize payload: nodes can come as array of objects; convert to ID list
        if ($request->has('nodes') && is_array($request->input('nodes'))) {
            $request->merge([
                'nodes' => array_values(array_filter(array_map(function ($n) {
                    if (is_array($n)) return $n['id'] ?? null;
                    return $n;
                }, $request->input('nodes'))))
            ]);
        }
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

        return response()->json($product->load(['nodes','category']), 201);
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
            'resources.cpu' => 'integer',
            'resources.ram' => 'integer',
            'resources.disk' => 'integer',
            'resources.ports' => 'integer',
            'resources.databases' => 'integer|min:0',
            'resources.backups' => 'integer|min:0',
            'is_active' => 'boolean',
            'is_hidden' => 'boolean',
            'nodes' => 'array',
            'nodes.*' => 'exists:nodes,id'
        ];
        // Normalize incoming payload
        if ($request->has('nodes') && is_array($request->input('nodes'))) {
            $request->merge([
                'nodes' => array_values(array_filter(array_map(function ($n) {
                    if (is_array($n)) return $n['id'] ?? null;
                    return $n;
                }, $request->input('nodes'))))
            ]);
        }
        if ($request->filled('category_id') === false && $request->has('category_id')) {
            // Convert empty string to null to avoid exists validator exploding
            $request->merge(['category_id' => null]);
            unset($rules['category_id']); // allow null silently
        }
        if (config('services.pterodactyl.is_pelican')) {
            $rules['resources.egg_id'] = 'integer';
        } else {
            $rules['resources.egg_id'] = 'integer';
            $rules['resources.nest_id'] = 'integer';
        }
        $validated = $request->validate($rules);

        $data = $validated;
        if (isset($validated['resources'])) {
            $current = $product->resources ?: [];
            $product->resources = array_merge($current, $validated['resources']);
            unset($data['resources']);
        }
        $product->update($data);

        if (isset($validated['nodes'])) {
            $product->nodes()->sync($validated['nodes']);
        }

        return $product->load(['nodes','category']);
    }

}
