<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Services\PterodactylService;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    protected $ptero;

    public function __construct(PterodactylService $ptero)
    {
        $this->ptero = $ptero;
    }

    public function index(Request $request)
    {
        return $request->user()->servers()->with('product', 'node')->get();
    }
    
    public function show(Request $request, $id)
    {
        return $request->user()->servers()->with('product', 'node')->findOrFail($id);
    }

    public function resources(Request $request, Server $server)
    {
        if ($request->user()->id !== $server->user_id) {
            abort(403);
        }

        if (!$server->identifier) {
             // Fallback: Try to fetch from Ptero using ID if identifier is missing (legacy)
             try {
                $details = $this->ptero->getServerDetails($server->ptero_server_id);
                $server->identifier = $details['identifier'];
                $server->save();
             } catch (\Exception $e) {
                 return response()->json(['error' => 'Server not found on Pterodactyl'], 404);
             }
        }

        $resources = $this->ptero->getServerResources($server->identifier);
        
        if (!$resources) {
             return response()->json(['state' => 'offline'], 200);
        }

        return response()->json($resources);
    }

    public function power(Request $request, Server $server)
    {
        if ($request->user()->id !== $server->user_id) {
            abort(403);
        }

        $request->validate([
            'signal' => 'required|in:start,stop,restart,kill'
        ]);

        try {
            if (!$server->identifier) {
                 $details = $this->ptero->getServerDetails($server->ptero_server_id);
                 $server->identifier = $details['identifier'];
                 $server->save();
            }

            $this->ptero->sendPowerAction($server->identifier, $request->signal);
            return response()->json(['message' => 'Power signal sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
