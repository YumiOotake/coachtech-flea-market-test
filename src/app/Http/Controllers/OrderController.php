<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class OrderController extends Controller
{
    public function create($item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);

        return view('orders.create', compact('item', 'user'));
    }

    public function store(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'jpy',        // 日本円
                    'unit_amount'  => $item->price, // 円はそのまま（ドルは×100が必要）
                    'product_data' => [
                        'name' => $item->name,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'payment_method' => $request->payment_method,
                'postal_code' => session('postal_code') ?? auth()->user()->profile?->postal_code,
                'address' => session('address') ?? auth()->user()->profile?->address,
                'building' => session('building') ?? auth()->user()->profile?->building,
            ],
            // 成功・キャンセル時のリダイレクト先
            'success_url' => route('orders.success', ['item_id' => $item_id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('orders.cancel', ['item_id' => $item_id]),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request, $item_id)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // session_idでStripeに決済完了を確認
        $session = Session::retrieve($request->session_id);

        // 念のため決済完了チェック（不正アクセス対策）
        if ($session->payment_status !== 'paid') {
            return redirect()->route('items.show', ['item_id' => $item_id]);
        }

        $user = auth()->user();

        Order::create([
            'user_id' => $user->id,
            'item_id' => $item_id,
            'postal_code' => $session->metadata->postal_code,
            'address' => $session->metadata->address,
            'building' => $session->metadata->building,
            'payment_method' => $session->metadata->payment_method,
        ]);

        return redirect()->route('mypage.index');
    }

    public function cancel($item_id)
    {
        return redirect()->route('orders.create', $item_id);
    }

    public function edit($item_id)
    {
        $item = Item::findOrFail($item_id);

        return view('orders.address', compact('item'));
    }

    public function update(AddressRequest $request, $item_id)
    {
        Item::findOrFail($item_id);

        $order = $request->only([
            'postal_code',
            'address',
            'building',
        ]);
        session($order);

        return redirect()->route('orders.create', ['item_id' => $item_id]);
    }
}
