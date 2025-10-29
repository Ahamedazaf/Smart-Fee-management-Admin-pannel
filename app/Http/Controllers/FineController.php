<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;

class FineController extends Controller
{
    public function index()
    {
        $fines = Fine::with('user')->latest()->paginate(20);
        return view('fines.index', compact('fines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'itemName' => 'required|array|min:1',
            'itemAmount' => 'required|array|min:1',
        ]);

        $breakdown = [];
        $total = 0;
        foreach ($request->itemName as $index => $name) {
            $amount = isset($request->itemAmount[$index]) ? (float)$request->itemAmount[$index] : 0;
            $breakdown[] = [
                'item_name' => $name,
                'amount' => $amount,
            ];
            $total += $amount;
        }

        Fine::create([
            'user_id'      => $request->user_id,
            'user_type'    => 'student',
            'total_amount' => $total,
            'details_json' => json_encode($breakdown),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fine invoice created successfully!',
        ]);
    }
}
