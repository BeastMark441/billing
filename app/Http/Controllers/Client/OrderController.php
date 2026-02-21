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
        ]);

        $product = Product::findOrFail($request->product_id);
        $user = $request->user();

        // Check if product is active and not hidden (unless allowed)
        if (!$product->is_active) {
            return response()->json(['error' => 'Product is not available'], 404);
        }

        // Simple balance check
        if ($user->balance < $product->price_monthly) {
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
                $user->decrement('balance', $product->price_monthly);

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
