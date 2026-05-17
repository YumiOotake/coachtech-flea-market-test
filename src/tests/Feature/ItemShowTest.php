<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Comment;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報が表示される(): void
    {
        $this->seed();
        $user = User::factory()->create([
            'name' => 'コメントユーザー',
        ]);
        $item = Item::factory()->create([
            'image' => 'test.png',
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 2000,
            'description' => 'テスト商品説明',
            'condition_id' => 1,
        ]);
        $item->categories()->attach(1);
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        Comment::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => '商品コメント',
        ]);

        $response = $this->get(route('items.show', ['item_id' => $item->id]));

        $response->assertStatus(200)
            ->assertSee('src="' . asset('storage/test.png') . '"', false)
            ->assertSee('テスト商品')
            ->assertSee('テストブランド')
            ->assertSee('2,000')
            ->assertSee('テスト商品説明')
            ->assertSee('ファッション')
            ->assertSee('<span class="item-show__like-count">1</span>', false)
            ->assertSee('<span class="item-show__comment-count">1</span>', false)
            ->assertSee('商品コメント')
            ->assertSee('コメントユーザー')
            ->assertSee('良好');
    }

    /** @test */
    public function 複数選択されたカテゴリが表示されているか(): void
    {
        $this->seed();
        $item = Item::factory()->create();
        $item->categories()->attach([1, 2, 3]);

        $response = $this->get(route('items.show', ['item_id' => $item->id]));

        $response->assertStatus(200)
            ->assertSee('ファッション')
            ->assertSee('家電')
            ->assertSee('インテリア');
    }
}
