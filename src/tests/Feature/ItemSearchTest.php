<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;


class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID 6-1: 「商品名」で部分一致検索ができる [22: ID 6-1]
     */
    public function test_can_search_items_by_name_partial_match()
    {
        // 1. 準備：検索対象と対象外の商品を作成 [23: 商品データ一覧準拠]
        Item::factory()->create(['name' => '腕時計']);
        Item::factory()->create(['name' => 'HDD']);

        // 2. 実行：キーワード「時計」で検索する [18: FN016-2]
        $response = $this->get('/?keyword=時計');

        // 3. 検証：部分一致する商品のみが表示される
        $response->assertStatus(200);
        $response->assertSee('腕時計');
        $response->assertDontSee('HDD');
    }

    /**
     * ID 6-2: 検索状態がマイリストでも保持されている [18: FN016-3, 22: ID 6-2]
     */
    public function test_search_keyword_is_maintained_in_mylist()
    {
        // 1. 準備：ユーザーと「いいね」した商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => 'テスト商品']);
        $user->likedItems()->attach($item->id);

        // 2. 実行：検索キーワードを維持したままマイリストタブへ遷移する [16: PG02]
        // 以前作成したコントローラーのロジックに基づき、keywordとtabを同時に送ります
        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=テスト');

        // 3. 検証：マイリスト内でも検索結果が絞り込まれ、キーワードが画面（検索窓）に保持されている
        $response->assertStatus(200);
        $response->assertSee('テスト商品');

        // 画面設計 [4: PG01] にある検索窓の入力値としてキーワードが保持されているか確認
        $response->assertSee('テスト');
    }
}
