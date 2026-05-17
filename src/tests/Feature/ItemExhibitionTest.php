<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ItemExhibitionTest extends TestCase
{
    use RefreshDatabase;

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
