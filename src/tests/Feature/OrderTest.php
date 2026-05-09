<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private function 購入完了データを作成する()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->profile()->create([
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テスト建物',
        ]);
        $item = Item::factory()->create([
            'name' => 'テスト商品'
        ]);

        $this->actingAs($user)
            ->get(route('orders.create', ['item_id' => $item->id]))
            ->assertStatus(200);

        Mockery::mock('alias:Stripe\Stripe')
            ->shouldReceive('setApiKey')
            ->once();

        Mockery::mock('alias:Stripe\Checkout\Session')
            ->shouldReceive('create')
            ->once()
            ->andReturn((object) [
                'url' => 'https://checkout.stripe.com/test-session',
            ]);

        $response = $this->actingAs($user)->post(route('orders.store', [
            'item_id' => $item->id,
        ]), [
            'payment_method' => 1,
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テスト建物',
        ]);

        $response->assertRedirect('https://checkout.stripe.com/test-session');

        $event = (object) [
            'type' => 'checkout.session.completed',
            'data' => (object) [
                'object' => (object) [
                    'metadata' => (object) [
                        'user_id' => (string) $user->id,
                        'item_id' => (string) $item->id,
                        'postal_code' => '123-4567',
                        'address' => 'テスト住所',
                        'building' => 'テスト建物',
                        'payment_method' => '1',
                    ],
                ],
            ],
        ];

        Mockery::mock('alias:Stripe\Webhook')
            ->shouldReceive('constructEvent')
            ->once()
            ->andReturn($event);

        $webhookResponse = $this->call(
            'POST',
            '/webhook/stripe',
            [],
            [],
            [],
            [
                'HTTP_Stripe-Signature' => 'test-signature',
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(['dummy' => 'payload'])
        );

        $webhookResponse->assertStatus(200);

        return [$user, $item];
    }

    /** @test */
    public function 「購入する」ボタンを押下すると購入が完了する(): void
    {
        [$user, $item] = $this->購入完了データを作成する();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テスト建物',
            'payment_method' => 1,
        ]);
    }

    /** @test */
    public function 購入した商品は商品一覧画面にて「sold」と表示される(): void
    {
        [$user, $item] = $this->購入完了データを作成する();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テスト建物',
            'payment_method' => 1,
        ]);

        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /** @test */
    public function 「プロフィールの購入した商品一覧」に追加されている(): void
    {
        [$user, $item] = $this->購入完了データを作成する();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テスト建物',
            'payment_method' => 1,
        ]);

        $response = $this->get(route('mypage.index', ['page' => 'buy']));

        $response->assertStatus(200);
        $response->assertSee('テスト商品');
    }

    // /** @test */
    // public function 小計画面で変更が反映される(): void
    // {
    //     $this->seed();

    //     $user = User::factory()->create();
    //     $user->profile()->create([
    //         'postal_code' => '123-4567',
    //         'address' => 'テスト住所',
    //         'building' => 'テスト建物',
    //     ]);
    //     $item = Item::factory()->create();

    //     $response = $this->actingAs($user)
    //         ->get(route('orders.create', ['item_id' => $item->id]));

    //     $response->assertStatus(200)
    //         ->assertSee('選択してください')
    //         ->assertSee('コンビニ支払い')
    //         ->assertSee('カード支払い')
    //         ->assertSee('未選択')
    //         ->assertSee('id="payment_method"', false)
    //         ->assertSee('id="payment_method_label"', false);
    // }

    /** @test */
    public function 送付先住所変更画面にて登録した住所が商品購入画面に反映されている(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $user->profile()->create([
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テスト建物',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user)->get(route(
            'orders.edit',
            ['item_id' => $item->id]
        ))
            ->assertStatus(200);

        $this->actingAs($user)
            ->patch(
                route('orders.update', ['item_id' => $item->id]),
                [
                    'postal_code' => '111-1111',
                    'address' => 'テスト住所変更',
                    'building' => 'テスト建物変更',
                ]
            );

        $response = $this->actingAs($user)
            ->get(route('orders.create', ['item_id' => $item->id]));

        $response->assertStatus(200)
            ->assertSee('111-1111')
            ->assertSee('テスト住所変更')
            ->assertSee('テスト建物変更');
    }

    /** @test */
    public function 購入した商品に送付先住所が紐づいて登録される(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $user->profile()->create([
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テスト建物',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user)
            ->patch(route('orders.update', ['item_id' => $item->id]), [
                'postal_code' => '111-1111',
                'address' => 'テスト住所変更',
                'building' => 'テスト建物変更',
            ])
            ->assertRedirect(route('orders.create', ['item_id' => $item->id]));

        Mockery::mock('alias:Stripe\Stripe')
            ->shouldReceive('setApiKey')
            ->once();

        Mockery::mock('alias:Stripe\Checkout\Session')
            ->shouldReceive('create')
            ->once()
            ->andReturn((object) [
                'url' => 'https://checkout.stripe.com/test-session',
            ]);

        $response = $this->actingAs($user)->post(route('orders.store', [
            'item_id' => $item->id,
        ]), [
            'payment_method' => 1,
            'postal_code' => '111-1111',
            'address' => 'テスト住所変更',
            'building' => 'テスト建物変更',
        ]);

        $response->assertRedirect('https://checkout.stripe.com/test-session');

        $event = (object) [
            'type' => 'checkout.session.completed',
            'data' => (object) [
                'object' => (object) [
                    'metadata' => (object) [
                        'user_id' => (string) $user->id,
                        'item_id' => (string) $item->id,
                        'postal_code' => '111-1111',
                        'address' => 'テスト住所変更',
                        'building' => 'テスト建物変更',
                        'payment_method' => '1',
                    ],
                ],
            ],
        ];

        Mockery::mock('alias:Stripe\Webhook')
            ->shouldReceive('constructEvent')
            ->once()
            ->andReturn($event);

        $webhookResponse = $this->call(
            'POST',
            '/webhook/stripe',
            [],
            [],
            [],
            [
                'HTTP_Stripe-Signature' => 'test-signature',
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(['dummy' => 'payload'])
        );

        $webhookResponse->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => '111-1111',
            'address' => 'テスト住所変更',
            'building' => 'テスト建物変更',
            'payment_method' => 1,
        ]);
    }
}
