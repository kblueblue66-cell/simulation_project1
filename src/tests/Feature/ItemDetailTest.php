<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Comment;


class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID 7-1: 必要な情報がすべて表示される [18: FN017-1, 22: ID 7-1]
     */
    public function test_item_detail_page_displays_all_required_information()
    {
        // 1. 準備：マスターデータと商品、コメントを作成
        $condition = Condition::factory()->create(['name' => '良好']);
        $category = Category::factory()->create(['name' => 'ファッション']);

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 47000,
            'description' => 'これはテスト商品の説明です。',
        ]);
        $item->categories()->attach($category->id);

        // コメントの作成 [18: FN017-1-10,11]
        $user = User::factory()->create(['name' => 'コメントユーザー']);
        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => 'テストコメント内容'
        ]);

        // 2. 実行：商品詳細ページへアクセス [16: PG05]
        // ※FN017-3に基づき、未認証状態でアクセス
        $response = $this->get("/item/{$item->id}");

        // 3. 検証：すべての情報が表示されているか [18: FN017-1]
        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('47,000');
        $response->assertSee('これはテスト商品の説明です。');
        $response->assertSee('良好');
        $response->assertSee('ファッション');
        $response->assertSee('コメントユーザー');
        $response->assertSee('テストコメント内容');
        // いいね数やコメント数のアイコン/数値の存在確認（デザイン [1] 参照）
        $response->assertSee('1');
    }

    /**
     * ID 7-2: 複数選択されたカテゴリが表示されている [18: FN017-2, 22: ID 7-2]
     */
    public function test_multiple_categories_are_displayed()
    {
        // 1. 準備：2つのカテゴリを持つ商品を作成
        $item = Item::factory()->create();
        $cat1 = Category::factory()->create(['name' => 'メンズ']);
        $cat2 = Category::factory()->create(['name' => '洋服']);
        $item->categories()->attach([$cat1->id, $cat2->id]);

        // 2. 実行
        $response = $this->get("/item/{$item->id}");

        // 3. 検証：両方のカテゴリ名が表示されている
        $response->assertSee('メンズ');
        $response->assertSee('洋服');
    }
}
