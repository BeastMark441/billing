<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;

class RebuildCache extends Command
{
    protected $signature = 'billing:cache:rebuild';
    protected $description = 'Rebuilds product-node cache from database';

    public function handle()
    {
        $this->info('Rebuilding product-node cache...');
        $count = 0;
        Product::with('nodes')->chunk(100, function ($products) use (&$count) {
            foreach ($products as $product) {
                $nodeIds = $product->nodes->pluck('id')->toArray();
                Cache::put("product_nodes:{$product->id}", $nodeIds, 86400);
                $count++;
            }
        });
        $this->info("Rebuilt cache entries for {$count} products.");
        return self::SUCCESS;
    }
}

