<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase;
/**
 * 出品商品情報登録：商品出品画面にて必要な情報が保存できる [22: ID 15]
 */
public function test_user_can_list_a_new_product()
{
    $user = User::factory()->create();
    $category = \App\Models\Category::create(['name' => 'ファッション']);
    $condition = \App\Models\Condition::create(['name' => '良好']);

    // ダミーの画像ファイルを作成 [18: FN029]
    $file = \Illuminate\Http\Testing\File::fake()->create('item.jpeg',100,'image/jpeg');

    $exhibitionData = [
        'name'         => 'テスト商品',
        'brand'        => 'テストブランド',
        'description'  => 'テスト用の商品の説明です。',
        'price'        => 5000,
        'condition_id' => $condition->id,
        'category_ids' => [$category->id], // 複数選択を想定 [18: FN028-2-2]
        'image'        => $file,
    ];

    //$this->withoutExceptionHandling();

    // 基本設計書に基づき /sell へPOSTリクエスト [1]
    $response = $this->actingAs($user)->post('/sell', $exhibitionData);

    // 出品後はトップページ等へ遷移（仕様に合わせて調整）
    $response->assertRedirect('/');

    // 1. itemsテーブルにデータが保存されているか [25: No. 3]
    $this->assertDatabaseHas('items', [
        'user_id' => $user->id,
        'name'    => 'テスト商品',
        'price'   => 5000,
    ]);

    // 2. 中間テーブルにカテゴリが紐付いているか [25: No. 5]
    $item = \App\Models\Item::where('name', 'テスト商品')->first();
    $this->assertDatabaseHas('category_item', [
        'item_id'     => $item->id,
        'category_id' => $category->id,
    ]);
}

}
