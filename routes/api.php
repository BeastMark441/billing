<?php

use App\Http\Controllers\Api\ReceiptApiController;
use App\Models\InfrastructureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::match(['GET'], '/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::match(['GET'], '/receipts', [ReceiptApiController::class, 'index']);
    Route::match(['GET'], '/receipts/{receipt}', [ReceiptApiController::class, 'show']);
});

use App\Services\PterodactylService;

Route::match(['GET'], '/pterodactyl/egg/{id}', function (int $id, PterodactylService $service) {
    $details = $service->getEggDetails($id);
    if ($details) {
        return response()->json($details);
    }

    return response()->json(['error' => 'Egg not found'], 404);
});

Route::match(['GET'], '/infrastructure/search', function (Request $request) {
    $query = (string) $request->query('q', '');

    if (strlen($query) < 2) {
        return response()->json([]);
    }

    $services = InfrastructureService::where('is_active', true)
        ->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('slug', 'like', "%{$query}%");
        })
        ->with(['category', 'subcategory'])
        ->limit(10)
        ->get()
        ->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'price' => $service->price,
                'specifications' => $service->specifications,
                'category_name' => $service->category->name,
                'subcategory_name' => $service->subcategory ? $service->subcategory->name : null,
            ];
        });

    return response()->json($services);
});
