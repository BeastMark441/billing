<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Node;

class BackupCriticalData extends Command
{
    protected $signature = 'billing:backup';
    protected $description = 'Backup critical data (products, nodes, product-node links) to storage/backups JSON';

    public function handle()
    {
        $data = [
            'timestamp' => now()->toIso8601String(),
            'products' => Product::with('nodes:id')->get()->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'resources' => $p->resources,
                    'node_ids' => $p->nodes->pluck('id')->toArray(),
                ];
            })->toArray(),
            'nodes' => Node::get()->map(function ($n) {
                return [
                    'id' => $n->id,
                    'name' => $n->name,
                    'ptero_id' => $n->ptero_id,
                    'ip' => $n->ip,
                    'public_host' => $n->public_host,
                    'is_active' => $n->is_active,
                ];
            })->toArray(),
        ];
        $dir = 'backups';
        $name = 'backup_' . now()->format('Ymd_His') . '.json';
        Storage::disk('local')->put($dir . '/' . $name, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $path = storage_path('app/' . $dir . '/' . $name);
        $this->info("Backup saved: {$path}");
        return self::SUCCESS;
    }
}

