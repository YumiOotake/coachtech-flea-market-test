<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $request->header('Stripe-Signature'),
            config('services.stripe.webhook_secret')
        );

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            Order::create([
                'user_id' => $session->metadata->user_id,
                'item_id' => $session->metadata->item_id,
                'postal_code' => $session->metadata->postal_code,
                'address' => $session->metadata->address,
                'building' => $session->metadata->building,
                'payment_method' => $session->metadata->payment_method,
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
