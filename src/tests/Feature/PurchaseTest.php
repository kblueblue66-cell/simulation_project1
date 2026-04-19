<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use Stripe\Checkout\Session as StripeSession;


class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 「購入する」ボタンを押下すると購入が完了する [22: ID 10-1]
     */
    public function test_user_can_complete_purchase()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create(['price' => 47000]);

        \Mockery::mock('alias:' . StripeSession::class)
        ->shouldReceive('create')
        ->andReturn((object)['url' => '/']);


        $purchaseData = [
            'item_id' => $item->id,
            'payment_method' => 'card', // [18: FN023]
            'post_code' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => 'テックビル',
        ];

        // 購入処理を実行 [24: PurchaseController@store]
        $response = $this->actingAs($user)->post("/purchase/{$item->id}", $purchaseData);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'item_id' => $item->id,
    ]);
    }

    /**
     * 購入した商品は商品一覧画面にて「sold」と表示される [22: ID 10-2]
     */
    public function test_purchased_item_shows_sold_label()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 購入済みの状態を作る [18: FN022-1]
        $this->actingAs($user)->post("/purchase/{$item->id}", [
            'payment_method' => 'card',
            'post_code' => '123-4567',
            'address' => '住所',
        ]);

        $response = $this->get('/');

        // 期待挙動：商品一覧で "Sold" ラベルが表示されていること [18: FN014-3]
        $response->assertSee('Sold');
    }

    /**
     * 「プロフィール/購入した商品一覧」に追加されている [22: ID 10-3]
     */
    public function test_purchased_item_is_added_to_profile()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => 'テスト商品']);

        // 購入処理を実行
        $this->actingAs($user)->post("/purchase/{$item->id}", [
            'payment_method' => 'card',
            'post_code' => '123-4567',
            'address' => '住所',
        ]);

        // マイページの「購入した商品一覧」タブを表示 [24: ProfileController@index]
        $response = $this->actingAs($user)->get('/mypage?page=buy');

        // 期待挙動：購入した商品名が表示されていること [18: FN022-3]
        $response->assertSee('テスト商品');
    }
    public function test_payment_method_selection_is_reflected_on_purchase_page(){
    $user = User::factory()->create();
    // プロフィール情報がないと住所表示でエラーになる可能性があるため作成 [18: FN021-4]
    \App\Models\Profile::factory()->create(['user_id' => $user->id]);
    $item = Item::factory()->create();

    // 1. 商品購入画面を開く [22: ID 11]
    // 2. 支払い方法を選択（通常はJavaScriptで反映されますが、サーバー側での反映を確認します）
    // クエリパラメータやセッション等で選択状態を渡す実装に合わせます
    $response = $this->actingAs($user)->get("/purchase/{$item->id}?payment_method=card");

    $response->assertStatus(200);

    // 期待挙動：選択した支払い方法（例：カード支払い）が画面に表示されていること [18: FN023-1, 22: ID 11]
    $response->assertSee('カード支払い');

    // コンビニ支払いを選択した場合の確認
    $response = $this->actingAs($user)->get("/purchase/{$item->id}?payment_method=konbini");
    $response->assertSee('コンビニ払い');
    }
    /**
 * 配送先変更機能：送付先住所変更画面にて登録した住所が商品購入画面に反映されている [22: ID 12]
 */
public function test_shipping_address_change_is_reflected_on_purchase_page()
{
    $user = User::factory()->create();
    \App\Models\Profile::factory()->create(['user_id' => $user->id]);
    $item = Item::factory()->create();

    // 1. 送付先住所変更画面で新しい住所を登録する [24: AddressController@update]
    $newAddressData = [
        'post_code' => '999-9999',
        'address'   => '変更後の住所',
        'building'  => '変更後の建物名',
    ];

    // 設計書 [1] に基づき、POSTリクエストで住所を更新
    $response = $this->actingAs($user)->post("/purchase/address/{$item->id}", $newAddressData);

    // 商品購入画面にリダイレクトされることを確認 [18: FN024-1]
    $response->assertRedirect("/purchase/{$item->id}");

    // 2. 商品購入画面を再度開き、登録した住所が表示されているか確認 [22: ID 12]
    $response = $this->get("/purchase/{$item->id}");

    $response->assertStatus(200);
    $response->assertSee('999-9999');
    $response->assertSee('変更後の住所');
    $response->assertSee('変更後の建物名');
}
}
