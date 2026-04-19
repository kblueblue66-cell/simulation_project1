<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;


class MyListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID 5: いいねした商品だけが表示される [22: ID 5-1]
     */
    public function test_can_view_only_liked_items()
    {
        // 1. 準備：ユーザーと商品を作成
        $user = User::factory()->create();
        $itemLiked = Item::factory()->create(['name' => 'いいねした商品']);
        $itemNotLiked = Item::factory()->create(['name' => 'いいねしてない商品']);

        // 商品にいいねを登録する [25: No.7]
        // ※UserモデルにlikedItemsリレーションが定義されている前提
        $user->likedItems()->attach($itemLiked->id);

        // 2. 実行：ログインしてマイリスト（/?tab=mylist）にアクセス [16: PG02, 24]
        $response = $this->actingAs($user)->get('/?tab=mylist');

        // 3. 検証：いいねした商品のみが表示され、してない商品は表示されない [18: FN015-1]
        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertDontSee('いいねしてない商品');
    }

    /**
     * ID 5: 購入済み商品は「Sold」と表示される [22: ID 5-2]
     */
    public function test_purchased_liked_item_shows_sold_label()
    {
        // 1. 準備：ユーザーが「いいね」しており、かつ「購入済み」の商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => '売り切れの商品']);
        $user->likedItems()->attach($item->id);

        // 注文データを作成して購入済みにする [25: No.6]
        Order::factory()->create(['item_id' => $item->id]);

        // 2. 実行：マイリストにアクセス
        $response = $this->actingAs($user)->get('/?tab=mylist');

        // 3. 検証：「Sold」が表示されている [18: FN015-3]
        $response->assertSee('売り切れの商品');
        $response->assertSee('Sold');
    }

    /**
     * ID 5: 未認証の場合は何も表示されない [22: ID 5-3]
     */
    public function test_unauthenticated_user_sees_nothing_in_mylist()
    {
        // 1. 準備：商品を作成（ログインはしない）
        Item::factory()->create(['name' => '適当な商品']);

        // 2. 実行：未ログイン状態でマイリストにアクセス
        $response = $this->get('/?tab=mylist');

        // 3. 検証：商品が表示されていない [18: FN015-4]
        $response->assertDontSee('適当な商品');
    }
}
