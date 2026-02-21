<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'balance' => $request->user()->balance,
            'history' => $request->user()->payments()->where('status', 'completed')->latest()->get(),
        ]);
    }
}
