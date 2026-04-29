<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use ReturnTypeWillChange;

class OrderController extends Controller
{
    public function create($item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);

        return view('orders.create', compact('item', 'user'));
    }

    public function store($item_id)
    {
        $user = auth()->user();

        Order::create([
            'user_id' => $user->id,
            'item_id' => $item_id,
            'postal_code' => session('postal_code') ?? $user->profile?->postal_code,
            'address' => session('address') ?? $user->profile?->address,
            'building' => session('building') ?? $user->profile?->building,
        ]);

        session()->forget([
            'postal_code',
            'address',
            'building',
        ]);

        return redirect()->route('items.index');
    }

    public function edit($item_id)
    {
        $item = Item::findOrFail($item_id);

        return view('orders.address', compact('item'));
    }

    public function update(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        $order = $request->only([
            'postal_code',
            'address',
            'building',
        ]);
        session($order);

        return redirect()->route('orders.create', ['item_id' => $item_id]);
    }
}
