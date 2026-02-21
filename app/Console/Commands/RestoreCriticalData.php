<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Node;

class RestoreCriticalData extends Command
{
    protected $signature = 'billing:restore {file}';
    protected $description = 'Restore critical data (product-node links) from storage/backups JSON';

    public function handle()
    {
        $file = $this->argument('file');
        if (!Storage::disk('local')->exists($file)) {
            $this->error("File not found in storage: {$file}");
            return self::FAILURE;
        }
        $json = Storage::disk('local')->get($file);
        $data = json_decode($json, true);
        if (!$data) {
            $this->error('Invalid JSON');
            return self::FAILURE;
        }
        $count = 0;
        foreach ($data['products'] ?? [] as $p) {
            $product = Product::find($p['id']);
            if (!$product) continue;
            $nodeIds = array_filter($p['node_ids'] ?? []);
            $product->nodes()->sync($nodeIds);
            $count++;
        }
        $this->info("Restored product-node links for {$count} products.");
        return self::SUCCESS;
    }
}

