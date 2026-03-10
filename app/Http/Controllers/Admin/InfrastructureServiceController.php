<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfrastructureCategory;
use App\Models\InfrastructureService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InfrastructureServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InfrastructureService::with('category')
            ->orderBy('infrastructure_category_id')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($request->filled('infrastructure_category_id')) {
            $query->where('infrastructure_category_id', $request->infrastructure_category_id);
        }

        $services = $query->paginate(20);

        return view('admin.infrastructure.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = InfrastructureCategory::with('subcategories')->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.infrastructure.services.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'infrastructure_category_id' => 'required|exists:infrastructure_categories,id',
            'infrastructure_subcategory_id' => 'nullable|exists:infrastructure_subcategories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:infrastructure_services',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'one_per_user' => 'boolean',

            // Pterodactyl Fields
            'pterodactyl.egg_id' => 'nullable|integer',
            'pterodactyl.nest_id' => 'nullable|integer',
            'pterodactyl.memory' => 'nullable|integer',
            'pterodactyl.disk' => 'nullable|integer',
            'pterodactyl.swap' => 'nullable|integer',
            'pterodactyl.cpu' => 'nullable|integer',
            'pterodactyl.io' => 'nullable|integer',
            'pterodactyl.databases' => 'nullable|integer',
            'pterodactyl.backups' => 'nullable|integer',
            'pterodactyl.allocations' => 'nullable|integer',
            'pterodactyl.startup' => 'nullable|string',
            'pterodactyl.docker_image' => 'nullable|string',
        ]);

        $data = $request->only([
            'infrastructure_category_id',
            'infrastructure_subcategory_id',
            'name',
            'slug',
            'description',
            'price',
            'sort_order',
            'is_active',
            'one_per_user',
        ]);

        // Process Pterodactyl specs
        if ($request->has('pterodactyl')) {
            $pterodactylData = $request->input('pterodactyl');
            // Filter out null values to keep JSON clean, or keep them if needed.
            // For now, let's keep keys but remove empty strings if any.
            $specs = array_filter($pterodactylData, function ($value) {
                return ! is_null($value) && $value !== '';
            });

            if (! empty($specs)) {
                $data['specifications'] = $specs;
            }
        }

        InfrastructureService::create($data);

        return redirect()->route('admin.infrastructure.services.index')
            ->with('success', 'Услуга создана.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InfrastructureService $service)
    {
        $categories = InfrastructureCategory::with('subcategories')->orderBy('sort_order')->orderBy('name')->get();
        // Prepare pterodactyl data for view if exists in specifications
        $pterodactyl = $service->specifications ?? [];

        return view('admin.infrastructure.services.edit', compact('service', 'categories', 'pterodactyl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InfrastructureService $service)
    {
        $validated = $request->validate([
            'infrastructure_category_id' => 'required|exists:infrastructure_categories,id',
            'infrastructure_subcategory_id' => 'nullable|exists:infrastructure_subcategories,id',
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('infrastructure_services')->ignore($service->id)],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'one_per_user' => 'boolean',

            // Pterodactyl Fields
            'pterodactyl.egg_id' => 'nullable|integer',
            'pterodactyl.nest_id' => 'nullable|integer',
            'pterodactyl.memory' => 'nullable|integer',
            'pterodactyl.disk' => 'nullable|integer',
            'pterodactyl.swap' => 'nullable|integer',
            'pterodactyl.cpu' => 'nullable|integer',
            'pterodactyl.io' => 'nullable|integer',
            'pterodactyl.databases' => 'nullable|integer',
            'pterodactyl.backups' => 'nullable|integer',
            'pterodactyl.allocations' => 'nullable|integer',
            'pterodactyl.startup' => 'nullable|string',
            'pterodactyl.docker_image' => 'nullable|string',
        ]);

        $data = $request->only([
            'infrastructure_category_id',
            'infrastructure_subcategory_id',
            'name',
            'slug',
            'description',
            'price',
            'sort_order',
            'is_active',
            'one_per_user',
        ]);

        // Process Pterodactyl specs
        if ($request->has('pterodactyl')) {
            $pterodactylData = $request->input('pterodactyl');
            $specs = array_filter($pterodactylData, function ($value) {
                return ! is_null($value) && $value !== '';
            });

            if (! empty($specs)) {
                $data['specifications'] = $specs;
            } else {
                $data['specifications'] = null;
            }
        }

        $service->update($data);

        return redirect()->route('admin.infrastructure.services.index')
            ->with('success', 'Услуга обновлена.');
    }

    public function show(InfrastructureService $service)
    {
        return redirect()->route('admin.infrastructure.services.edit', $service);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InfrastructureService $service)
    {
        $service->delete();

        return redirect()->route('admin.infrastructure.services.index')
            ->with('success', 'Услуга удалена.');
    }
}
