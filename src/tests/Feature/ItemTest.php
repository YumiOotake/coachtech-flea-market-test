<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Like;
use App\Models\Order;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    //商品一覧取得
    /** @test */
    public function 全商品を取得できる(): void
    {
        $this->seed();
        Item::factory()->create([
            'name' => 'テスト商品1',
        ]);
        Item::factory()->create([
            'name' => 'テスト商品2',
        ]);
        Item::factory()->create([
            'name' => 'テスト商品3',
        ]);

        $response = $this->get(route('items.index'));

        $response->assertStatus(200)
            ->assertSee('テスト商品1')
            ->assertSee('テスト商品2')
            ->assertSee('テスト商品3');
    }

    /** @test */
    public function 購入済み商品は「Sold」と表示される(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
        ]);

        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'payment_method' => 1,
        ]);

        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /** @test */
    public function 自分が出品した商品は表示されない(): void
    {
        $this->seed();
        $user = User::factory()->create();
        Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
        ]);

        $response = $this->actingAs($user)->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertDontSee('テスト商品');
    }

    //マイリスト一覧取得
    /** @test */
    public function いいねした商品だけが表示される(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $likedItem = Item::factory()->create([
            'name' => 'テスト商品1',
        ]);
        $item = Item::factory()->create([
            'name' => 'テスト商品2',
        ]);
        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        $response = $this->actingAs($user)->get(route('items.index', [
            'tab' => 'mylist'
        ]));

        $response->assertStatus(200)
            ->assertSee('テスト商品1')
            ->assertDontSee('テスト商品2');
    }

    /** @test */
    public function 購入済み商品は’Sold’と表示される(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        Order::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'payment_method' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('items.index', [
            'tab' => 'mylist'
        ]));

        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /** @test */
    public function 未認証の場合は何も表示されない(): void
    {
        $this->seed();
        $user = User::factory()->create();
        Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
        ]);

        $response = $this->get(route('items.index', [
            'tab' => 'mylist'
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('テスト商品');
    }

    //商品検索機能
    /** @test */
    public function 「商品名」で部分一致検索ができる(): void
    {
        $this->seed();
        Item::factory()->create([
            'name' => '一致する商品',
        ]);
        Item::factory()->create([
            'name' => '一致しない商品',
        ]);

        $response = $this->get(route('items.index', [
            'keyword' => '一致する',
        ]));

        $response->assertStatus(200);
        $response->assertSee('一致する商品')
            ->assertDontSee('一致しない商品');
    }

    /** @test */
    public function 検索状態がマイリストでも保持されている(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $mylistItem = Item::factory()->create([
            'name' => '一致する商品',
        ]);
        Like::create([
            'user_id' => $user->id,
            'item_id' => $mylistItem->id,
        ]);
        Item::factory()->create([
            'name' => '一致しない商品',
        ]);

        $response = $this->actingAs($user)->get(route('items.index', [
            'keyword' => '一致する',
        ]));

        $response->assertStatus(200)
            ->assertSee('一致する商品')
            ->assertDontSee('一致しない商品')
            ->assertSee('value="一致する"', false);

        $response = $this->actingAs($user)->get(route('items.index', [
            'tab' => 'mylist',
            'keyword' => '一致する',
        ]));

        $response->assertStatus(200)
            ->assertSee('一致する商品')
            ->assertDontSee('一致しない商品')
            ->assertSee('value="一致する"', false);
    }

    //商品詳細情報取得
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

    //出品商品情報登録
    /** @test */
    public function 商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、ブランド名、商品の説明、販売価格）(): void
    {
        $this->seed();
        $user = User::factory()->create();
        Storage::fake('public');

        $this->actingAs($user)->get(route('items.create'))
            ->assertStatus(200);

        $image = UploadedFile::fake()->create('item.png');
        $this->actingAs($user)->post(route('items.store'), [
            'image' => $image,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 2000,
            'description' => 'テスト商品説明',
            'condition_id' => 1,
            'category_id' => [1],
        ])->assertRedirect(route('mypage.index'));

        $item = Item::where('name', 'テスト商品')->first();

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 2000,
            'description' => 'テスト商品説明',
            'condition_id' => 1,
        ]);
        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => 1,
        ]);
        Storage::disk('public')->assertExists($item->image);
    }
}
