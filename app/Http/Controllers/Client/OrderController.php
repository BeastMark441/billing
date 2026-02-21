<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\ServerProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $provisioningService;

    public function __construct(ServerProvisioningService $provisioningService)
    {
        $this->provisioningService = $provisioningService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'use_trial' => 'sometimes|boolean',
        ]);

        $product = Product::findOrFail($request->product_id);
        $user = $request->user();

        // Check if product is active and not hidden (unless allowed)
        if (!$product->is_active) {
            return response()->json(['error' => 'Product is not available'], 404);
        }

        // Trial flow
        if ($request->boolean('use_trial')) {
            $trial = $product->trials()->where('active', true)->first();
            if (!$trial) {
                return response()->json(['error' => 'Trial is not available for this product'], 422);
            }
            $usedCount = Order::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->where('status', 'trial')
                ->count();
            if ($usedCount >= $trial->max_per_user) {
                return response()->json(['error' => 'Trial limit reached for this product'], 409);
            }
            try {
                return DB::transaction(function () use ($user, $product, $trial) {
                    $order = Order::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'amount' => 0,
                        'status' => 'trial',
                    ]);
                    // Provision server with trial expiration
                    $server = $this->provisioningService->provision($order);
                    $server->expires_at = now()->addDays($trial->duration_days);
                    $server->save();
                    \Log::info('Trial order created', [
                        'order_id' => $order->id,
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'duration_days' => $trial->duration_days,
                    ]);
                    return response()->json([
                        'message' => 'Trial activated and server provisioned',
                        'order' => $order,
                        'server' => $server,
                    ], 201);
                });
            } catch (\Exception $e) {
                return response()->json(['error' => 'Order processing failed: ' . $e->getMessage()], 500);
            }
        }

        // Simple balance check for paid flow
        if ($user->balance < $product->price_monthly) {
            \Log::warning('Insufficient balance for order', [
                'user_id' => $user->id,
                'balance' => $user->balance,
                'price' => $product->price_monthly,
            ]);
            return response()->json(['error' => 'Insufficient balance'], 402);
        }

        try {
            return DB::transaction(function () use ($user, $product) {
                // Create Order
                $order = Order::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'amount' => $product->price_monthly,
                    'status' => 'paid', // Immediately paid via balance
                ]);

                // Deduct Balance
                $oldBalance = $user->balance;
                $user->decrement('balance', $product->price_monthly);
                \Log::info('Balance deducted for order', [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'old_balance' => $oldBalance,
                    'amount' => $product->price_monthly,
                    'new_balance' => $user->balance - 0, // value after decrement in memory may be stale; for logs we compute anyway
                ]);

                // Record Payment
                $order->payments()->create([
                    'user_id' => $user->id,
                    'amount' => $product->price_monthly,
                    'gateway' => 'balance',
                    'status' => 'completed',
                ]);

                // Provision Server
                $server = $this->provisioningService->provision($order);

                return response()->json([
                    'message' => 'Order placed and server provisioned successfully',
                    'order' => $order,
                    'server' => $server,
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'Order processing failed: ' . $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        return $request->user()->orders()->with('product')->get();
    }
}
