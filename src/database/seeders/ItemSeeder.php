<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Condition;
use App\Models\User;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $params = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'images/Armani+Mens+Clock.jpg',
                'condition' => '良好',
                'categories' => ['ファッション', 'メンズ', 'アクセサリー'],
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'images/HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
                'categories' => ['家電'],
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'images/iLoveIMG+d.jpg',
                'condition' => 'やや傷や汚れあり',
                'categories' => ['キッチン'],
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'images/Leather+Shoes+Product+Photo.jpg',
                'condition' => '状態が悪い',
                'categories' => ['ファッション', 'メンズ'],
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'image' => 'images/Living+Room+Laptop.jpg',
                'condition' => '良好',
                'categories' => ['家電'],
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'image' => 'images/Music+Mic+4632231.jpg',
                'condition' => '目立った傷や汚れなし',
                'categories' => ['家電'],
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'images/Purse+fashion+pocket.jpg',
                'condition' => 'やや傷や汚れあり',
                'categories' => ['ファッション', 'レディース'],
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'image' => 'images/Tumbler+souvenir.jpg',
                'condition' => '状態が悪い',
                'categories' => ['キッチン'],
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image' => 'images/Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
                'categories' => ['キッチン'],
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'image' => 'images/外出メイクアップセット.jpg',
                'condition' => '目立った傷や汚れなし',
                'categories' => ['コスメ', 'レディース'],
            ],
        ];

        foreach ($params as $param) {
            $conditionId = Condition::where('name', $param['condition'])
                ->first()->id;

            $item = Item::create([
                'name' => $param['name'],
                'price' => $param['price'],
                'brand' => $param['brand'],
                'description' => $param['description'],
                'image' => $param['image'],
                'user_id' => User::all()->random()->id,
                'condition_id' => $conditionId,
            ]);

            $categoryIds = Category::whereIn('name', $param['categories'])->pluck('id');

            $item->categories()->attach($categoryIds);
        }
    }
}
