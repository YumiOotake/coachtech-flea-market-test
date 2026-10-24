<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent(); //Webhook の中身そのもの
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $request->header('Stripe-Signature'),
            config('services.stripe.webhook_secret')
        ); //署名つきの通知を検証して、安全なら $event として使う

        if ($event->type === 'checkout.session.completed') { //Checkout の支払い完了の時だけ
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

        return response()->json(['status' => 'ok']); //Stripe から来た通知に対して、サーバーとして正常応答を返す
    }
}
