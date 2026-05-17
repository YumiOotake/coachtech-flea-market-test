<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MypageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）(): void
    {
        $this->seed();

        $user = User::factory()->create([
            'name' => 'テストユーザー'
        ]);
        $user->profile()->create([
            'profile_image' => 'test.png',
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テスト建物',
        ]);
        $otherUser = User::factory()->create();
        $buyItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'name' => '購入した商品名',
        ]);
        Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品した商品名',
        ]);
        Order::create([
            'user_id' => $user->id,
            'item_id' => $buyItem->id,
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テスト建物',
            'payment_method' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('mypage.index', ['page' => 'sell']));

        $response->assertStatus(200)
            ->assertSee('src="' . asset('storage/test.png') . '"', false)
            ->assertSee('テストユーザー')
            ->assertSee('出品した商品名')
            ->assertDontSee('購入した商品名');

        $response = $this->actingAs($user)->get(route('mypage.index', ['page' => 'buy']));

        $response->assertStatus(200)
            ->assertSee('src="' . asset('storage/test.png') . '"', false)
            ->assertSee('テストユーザー')
            ->assertDontSee('出品した商品名')
            ->assertSee('購入した商品名');
    }

    /** @test */
    public function 変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）(): void
    {
        $this->seed();

        $user = User::factory()->create([
            'name' => 'テストユーザー'
        ]);
        $user->profile()->create([
            'profile_image' => 'test.png',
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
        ]);

        $this->actingAs($user)->get(route('mypage.index'))
            ->assertStatus(200);

        $response = $this->actingAs($user)->get(route('mypage.edit'));

        $response->assertStatus(200)
            ->assertSee('value="テストユーザー"', false)
            ->assertSee('src="' . asset('storage/test.png') . '"', false)
            ->assertSee('value="123-4567"', false)
            ->assertSee('value="テスト住所"', false);
    }
}
