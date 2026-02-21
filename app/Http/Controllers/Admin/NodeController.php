<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Node;
use Illuminate\Http\Request;

class NodeController extends Controller
{
    public function index()
    {
        return Node::orderBy('id', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ptero_id' => 'required|integer',
            'ip' => 'required|string|max:255',
            'port_range_start' => 'required|integer',
            'port_range_end' => 'required|integer|gte:port_range_start',
            'is_active' => 'boolean',
        ]);

        $node = Node::create($validated);
        return response()->json($node, 201);
    }

    public function show(Node $node)
    {
        return $node;
    }

    public function update(Request $request, Node $node)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'ptero_id' => 'sometimes|integer',
            'ip' => 'sometimes|string|max:255',
            'port_range_start' => 'sometimes|integer',
            'port_range_end' => 'sometimes|integer|gte:port_range_start',
            'is_active' => 'boolean',
        ]);

        $node->update($validated);
        return $node;
    }

    public function destroy(Node $node)
    {
        $node->delete();
        return response()->noContent();
    }
}
