<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Services\PterodactylService;

class CheckServerExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:check-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired servers and suspend them';

    /**
     * Execute the console command.
     */
    public function handle(PterodactylService $ptero)
    {
        $expiredServers = Server::where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredServers as $server) {
            // Check grace period logic here (e.g., if expires_at + grace_period < now)
            // For MVP, strict expiration -> suspend
            try {
                $ptero->suspendServer($server->ptero_server_id);
                $server->status = 'suspended';
                $server->save();
                $this->info("Suspended server {$server->id} (Expired: {$server->expires_at})");
            } catch (\Exception $e) {
                $this->error("Failed to suspend server {$server->id}: " . $e->getMessage());
            }
        }
        
        // Logic for termination (delete) after X days can be added here
    }
}
