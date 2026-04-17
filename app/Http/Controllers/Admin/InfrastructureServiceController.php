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

        if ($request->filled('integration_type')) {
            if ($request->integration_type === 'none') {
                $query->whereNull('integration_type');
            } else {
                $query->where('integration_type', $request->integration_type);
            }
        }

        $services = $query->paginate(20)->withQueryString();

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
            'integration_type' => 'required|in:pterodactyl,proxmoxve,service,other',

            // Pterodactyl Fields
            'pterodactyl.egg_id' => 'required_if:integration_type,pterodactyl|nullable|integer',
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

            // ProxmoxVE Fields
            'proxmox.node' => 'required_if:integration_type,proxmoxve|nullable|string|max:255',
            'proxmox.type' => 'required_if:integration_type,proxmoxve|nullable|in:lxc,qemu',
            'proxmox.template_vmid' => 'required_if:integration_type,proxmoxve|nullable|integer|min:1',
            'proxmox.storage' => 'nullable|string|max:255',
            'proxmox.bridge' => 'nullable|string|max:255',
            'proxmox.cores' => 'nullable|integer|min:1',
            'proxmox.memory_mb' => 'nullable|integer|min:128',
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
            'integration_type',
        ]);

        if ($validated['integration_type'] === 'pterodactyl') {
            $pterodactylData = $request->input('pterodactyl', []);
            $specs = array_filter($pterodactylData, fn ($value) => ! is_null($value) && $value !== '');
            $data['specifications'] = ! empty($specs) ? $specs : null;
        } elseif ($validated['integration_type'] === 'proxmoxve') {
            $proxmoxData = $request->input('proxmox', []);
            $specs = array_filter($proxmoxData, fn ($value) => ! is_null($value) && $value !== '');
            $data['specifications'] = ! empty($specs) ? ['proxmox' => $specs] : null;
        } else {
            $data['specifications'] = null;
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
        $specifications = $service->specifications ?? [];

        return view('admin.infrastructure.services.edit', compact('service', 'categories', 'specifications'));
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
            'integration_type' => 'required|in:pterodactyl,proxmoxve,service,other',

            // Pterodactyl Fields
            'pterodactyl.egg_id' => 'required_if:integration_type,pterodactyl|nullable|integer',
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

            // ProxmoxVE Fields
            'proxmox.node' => 'required_if:integration_type,proxmoxve|nullable|string|max:255',
            'proxmox.type' => 'required_if:integration_type,proxmoxve|nullable|in:lxc,qemu',
            'proxmox.template_vmid' => 'required_if:integration_type,proxmoxve|nullable|integer|min:1',
            'proxmox.storage' => 'nullable|string|max:255',
            'proxmox.bridge' => 'nullable|string|max:255',
            'proxmox.cores' => 'nullable|integer|min:1',
            'proxmox.memory_mb' => 'nullable|integer|min:128',
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
            'integration_type',
        ]);

        if ($validated['integration_type'] === 'pterodactyl') {
            $pterodactylData = $request->input('pterodactyl', []);
            $specs = array_filter($pterodactylData, fn ($value) => ! is_null($value) && $value !== '');
            $data['specifications'] = ! empty($specs) ? $specs : null;
        } elseif ($validated['integration_type'] === 'proxmoxve') {
            $proxmoxData = $request->input('proxmox', []);
            $specs = array_filter($proxmoxData, fn ($value) => ! is_null($value) && $value !== '');
            $data['specifications'] = ! empty($specs) ? ['proxmox' => $specs] : null;
        } else {
            $data['specifications'] = null;
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
