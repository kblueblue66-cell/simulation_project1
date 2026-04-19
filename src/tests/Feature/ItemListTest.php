<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID 4: 全商品を取得できる [22: ID 4-1]
     */
    public function test_can_retrieve_all_items()
    {
        // 1. 準備：複数の商品を作成 [1]
        Item::factory()->create(['name' => '腕時計']);
        Item::factory()->create(['name' => 'HDD']);

        // 2. 実行：トップページを開く [2, 3]
        $response = $this->get('/');

        // 3. 検証：すべての商品名が表示されている [18: FN014-1,2]
        $response->assertStatus(200);
        $response->assertSee('腕時計');
        $response->assertSee('HDD');
    }

    /**
     * ID 4: 購入済み商品は「Sold」と表示される [22: ID 4-2]
     */
    public function test_purchased_item_shows_sold_label()
    {
        // 1. 準備：商品とそれ紐づく注文（購入記録）を作成 [25: No.6]
        $item = Item::factory()->create(['name' => '玉ねぎ3束']);
        Order::factory()->create(['item_id' => $item->id]);

        // 2. 実行：トップページを開く
        $response = $this->get('/');

        // 3. 検証：商品名と共に「Sold」が表示されている [18: FN014-3]
        $response->assertSee('玉ねぎ3束');
        $response->assertSee('Sold');
    }

    /**
     * ID 4: 自分が出品した商品は表示されない [22: ID 4-3]
     */
    public function test_own_items_are_not_displayed()
    {
        // 1. 準備：ユーザーAとユーザーB、それぞれの商品を作成
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $itemA = Item::factory()->create([
            'user_id' => $userA->id,
            'name' => '自分（A）の商品'
        ]);
        $itemB = Item::factory()->create([
            'user_id' => $userB->id,
            'name' => '他人（B）の商品'
        ]);

        // 2. 実行：ユーザーAとしてログインしてトップページを開く [18: FN014-4]
        $response = $this->actingAs($userA)->get('/');

        // 3. 検証：他人の商品は見えるが、自分の商品は見えない
        $response->assertSee('他人（B）の商品');
        $response->assertDontSee('自分（A）の商品');
    }
}

