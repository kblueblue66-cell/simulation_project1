<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Order;


class ProfileTest extends TestCase
{
    use RefreshDatabase;
    /**
 * ユーザー情報取得：必要な情報（名前、画像、出品・購入リスト）が表示される [22: ID 13]
 */
public function test_user_profile_information_is_displayed()
{
    $user = User::factory()->create();
    // プロフィール情報（画像、住所等）を作成 [25: No. 2]
    Profile::factory()->create([
        'user_id' => $user->id,
        'image_url' => 'test_profile.jpg'
    ]);

    // 自分が「出品した」商品
    $soldItem = Item::factory()->create(['user_id' => $user->id, 'name' => '出品した商品名']);

    // 自分が「購入した」商品
    $boughtItem = Item::factory()->create(['name' => '購入した商品名']);
    \App\Models\Order::create([
        'user_id' => $user->id,
        'item_id' => $boughtItem->id,
        'post_code' => '123-4567',
        'address' => 'テスト住所',
        'payment_method' => now(),
    ]);

    $response = $this->actingAs($user)->get('/mypage');

    $response->assertStatus(200);
    // 必要な情報が表示されているか [18: FN025]
    $response->assertSee($user->name);
    $response->assertSee('test_profile.jpg');
    $response->assertSee('出品した商品名');

    // 購入した商品リストのタブを確認する場合（PG11） [16, 18: FN025-4]
    $response = $this->actingAs($user)->get('/mypage?page=buy');
    $response->assertSee('購入した商品名');
}
/**
 * ユーザー情報変更：変更項目に過去の設定値が初期値として表示されている [22: ID 14]
 */
public function test_profile_edit_page_shows_initial_values()
{
    $user = User::factory()->create(['name' => 'テスト太郎']);
    // プロフィール情報を作成 [25: No. 2]
    Profile::factory()->create([
        'user_id'   => $user->id,
        'post_code' => '123-4567',
        'address'   => '東京都渋谷区',
        'building'  => 'テックビル',
    ]);

    // プロフィール編集画面（PG10）へアクセス [1, 2]
    $response = $this->actingAs($user)->get('/mypage/profile');

    $response->assertStatus(200);

    // 各項目が初期値として入力されているか [18: FN027, 22: ID 14]
    $response->assertSee('value="テスト太郎"', false);
    $response->assertSee('value="123-4567"', false);
    $response->assertSee('value="東京都渋谷区"', false);
    $response->assertSee('value="テックビル"', false);
}
}
