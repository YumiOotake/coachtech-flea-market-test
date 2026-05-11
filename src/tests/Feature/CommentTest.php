<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン済みのユーザーはコメントを送信できる(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)->post(route(
            'comment.store',
            ['item_id' => $item->id,]
        ), ['content' => '商品コメント']);

        $response = $this->actingAs($user)->get(route('items.show', [
            'item_id' => $item->id
        ]));

        $response->assertStatus(200)
            ->assertSee('<span class="item-show__comment-count">1</span>', false);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => '商品コメント'
        ]);
    }

    /** @test */
    public function ログイン前のユーザーはコメントを送信できない(): void
    {
        $this->seed();
        $item = Item::factory()->create();

        $response = $this->post(route(
            'comment.store',
            ['item_id' => $item->id,]
        ), ['content' => '商品コメント']);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => '商品コメント'
        ]);
    }

    /** @test */
    public function コメントが入力されていない場合、バリデーションメッセージが表示される(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(route(
            'comment.store',
            ['item_id' => $item->id,]
        ), ['content' => '']);

        $response->assertSessionHasErrors(['content' => '商品コメントを入力してください']);
    }

    /** @test */
    public function コメントが255字以上の場合、バリデーションメッセージが表示される(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(route(
            'comment.store',
            ['item_id' => $item->id,]
        ), ['content' => str_repeat('a', 256)]);

        $response->assertSessionHasErrors(['content' => '255文字以内で入力してください']);
    }
}
