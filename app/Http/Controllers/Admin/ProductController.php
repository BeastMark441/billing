<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        $activeServers = $product->servers()->where('status', 'active')->count();
        if ($activeServers > 0) {
            return response()->json([
                'error' => 'Невозможно удалить тариф, используемый активными пользователями',
                'code' => 'PRODUCT_IN_USE',
                'details' => [
                    'active_servers' => $activeServers,
                ],
            ], 409);
        }
        $product->nodes()->detach();
        $product->delete();
        return response()->noContent();
    }


    public function show(Product $product)
    {
        return $product->load('nodes');
    }

    public function update(Request $request, Product $product, \App\Services\PterodactylService $ptero)
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

        $beforeResources = $product->resources ?? [];
        $data = $validated;
        if (isset($validated['resources'])) {
            $current = $product->resources ?: [];
            $merged = array_merge($current, $validated['resources']);
            $product->resources = $merged;
            unset($data['resources']);
        }
        $product->update($data);

        if (isset($validated['nodes'])) {
            $product->nodes()->sync($validated['nodes']);
        }

        // Sync servers on resource changes
        if (isset($beforeResources) && isset($product->resources) && $beforeResources !== $product->resources) {
            $changed = [];
            foreach (['cpu','ram','disk','databases','ports','backups'] as $k) {
                if (($beforeResources[$k] ?? null) !== ($product->resources[$k] ?? null)) {
                    $changed[$k] = ['from' => $beforeResources[$k] ?? null, 'to' => $product->resources[$k] ?? null];
                }
            }
            $servers = $product->servers()->where('status', 'active')->get();
            $limits = [
                'memory' => $product->resources['ram'] ?? 1024,
                'swap' => 0,
                'disk' => $product->resources['disk'] ?? 10240,
                'io' => 500,
                'cpu' => $product->resources['cpu'] ?? 100,
            ];
            $featureLimits = [
                'databases' => $product->resources['databases'] ?? 0,
                'allocations' => max(0, ($product->resources['ports'] ?? 1) - 1),
                'backups' => $product->resources['backups'] ?? 0,
            ];
            $updatedCount = 0;
            foreach ($servers as $srv) {
                try {
                    $ptero->updateServerBuild($srv->ptero_server_id, $limits, $featureLimits);
                    $updatedCount++;
                } catch (\Exception $e) {
                    \Log::error('Failed to update server build on plan change', [
                        'server_id' => $srv->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            // Audit log
            try {
                \App\Models\ProductAudit::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()->id ?? null,
                    'action' => 'updated',
                    'changes' => $changed,
                    'servers_updated' => $updatedCount,
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Failed to write product audit', ['error' => $e->getMessage()]);
            }
            // Optional webhook/callback to notify Pelican/external systems
            $webhookUrl = config('services.pterodactyl.sync_webhook_url');
            $webhookSecret = config('services.pterodactyl.sync_webhook_secret');
            if (!empty($webhookUrl)) {
                $payload = [
                    'event' => 'product.updated',
                    'product_id' => $product->id,
                    'changes' => $changed,
                    'servers_updated' => $updatedCount,
                    'timestamp' => now()->toIso8601String(),
                ];
                $signature = '';
                if (!empty($webhookSecret)) {
                    $signature = hash_hmac('sha256', json_encode($payload), $webhookSecret);
                }
                try {
                    Http::withHeaders([
                        'X-Webhook-Signature' => $signature,
                    ])->post($webhookUrl, $payload);
                } catch (\Throwable $e) {
                    \Log::warning('Pelican sync webhook failed', ['error' => $e->getMessage()]);
                }
            }
        }

        return $product->load(['nodes','category']);
    }

}
