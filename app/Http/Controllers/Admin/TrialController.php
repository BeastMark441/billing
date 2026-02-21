<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trial;
use Illuminate\Http\Request;

class TrialController extends Controller
{
    public function index()
    {
        return Trial::with('product')->orderBy('id', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'duration_days' => 'required|integer|min:1',
            'max_per_user' => 'required|integer|min:1',
            'active' => 'boolean',
        ]);

        return Trial::create($validated);
    }

    public function update(Request $request, Trial $trial)
    {
        $validated = $request->validate([
            'product_id' => 'sometimes|required|exists:products,id',
            'duration_days' => 'sometimes|required|integer|min:1',
            'max_per_user' => 'sometimes|required|integer|min:1',
            'active' => 'boolean',
        ]);

        $trial->update($validated);
        return $trial->load('product');
    }

    public function destroy(Trial $trial)
    {
        $trial->delete();
        return response()->json(['status' => 'ok']);
    }
}

