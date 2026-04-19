<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * いいねアイコンを押下することによって、いいねした商品として登録できるか
     * [22: ID 8-1]
     */
    public function test_user_can_like_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // ログインして商品詳細ページでいいねを実行
        $response = $this->actingAs($user)->post("/item/{$item->id}/like");

        // 期待挙動：いいねが登録され、合計値が増加していること [18: FN018-1-a]
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);
        // 合計値が1になっていることを確認（画面設計PG05に基づく表示）
        $response->assertSee('1');
    }

    /**
     * 追加済みのアイコンは色が変化するか
     * [22: ID 8-2]
     */
    public function test_like_icon_changes_color_when_liked()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // すでにいいねした状態を作る
        $this->actingAs($user)->post("/item/{$item->id}/like");

        $response = $this->get("/item/{$item->id}");

        // 期待挙動：色が変化した状態（特定のCSSクラスや画像など）が表示されていること [18: FN018-2]
        // ※実装しているクラス名に合わせて修正してください（例: text-red, is-liked など）
        $response->assertSee('is-liked');
    }

    /**
     * 再度いいねアイコンを押下することによって、いいねを解除できるか
     * [22: ID 8-3]
     */
    public function test_user_can_unlike_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 1回目の押下（いいね登録）
        $this->actingAs($user)->post("/item/{$item->id}/like");

        // 2回目の押下（いいね解除）
        $response = $this->actingAs($user)->post("/item/{$item->id}/like");

        // 期待挙動：いいねが解除され、データベースから消えていること [18: FN018-3]
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get("/item/{$item->id}");
        // 期待挙動：合計値が減少（0）していること [18: FN018-3-a]
        $response->assertSee('0');
    }
}
