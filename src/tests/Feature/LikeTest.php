<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねアイコンを押下することによって、いいねした商品として登録することができる(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)->get(route('items.show', [
            'item_id' => $item->id
        ]));

        $this->actingAs($user)->post(route('like.store', [
            'item_id' => $item->id
        ]));

        $response = $this->actingAs($user)->get(route('items.show', [
            'item_id' => $item->id
        ]));

        $response->assertSee('1');
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function 追加済みのアイコンは色が変化する(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->get(route('items.show', [
            'item_id' => $item->id
        ]));

        $response->assertSee('src="' . asset('storage/' . 'images/ハートロゴ_デフォルト.png') . '"', false);

        $this->actingAs($user)->post(route('like.store', [
            'item_id' => $item->id
        ]));

        $response = $this->actingAs($user)->get(route('items.show', [
            'item_id' => $item->id
        ]));

        $response->assertSee('src="' . asset('storage/' . 'images/ハートロゴ_ピンク.png') . '"', false);
    }

    /** @test */
    public function 再度いいねアイコンを押下することによって、いいねを解除することができる(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $item = Item::factory()->create();
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user)->get(route('items.show', [
            'item_id' => $item->id
        ]));

        $this->actingAs($user)->delete(route('like.destroy', [
            'item_id' => $item->id
        ]));

        $response = $this->actingAs($user)->get(route('items.show', [
            'item_id' => $item->id
        ]));

        $response->assertSee('0');
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
