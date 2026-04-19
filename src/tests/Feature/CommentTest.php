<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;


class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン済みのユーザーはコメントを送信できる [22: ID 9-1]
     */
    public function test_logged_in_user_can_send_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $commentData = [
            'content' => 'これはテストコメントです。'
        ];

        // ログインしてコメントを送信
        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", $commentData);

        // 期待挙動：データベースに保存されていること [25: No.8]
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'これはテストコメントです。',
        ]);

        // 期待挙動：商品詳細画面でコメント数が増加していること [18: FN020-3]
        $response = $this->get("/item/{$item->id}");
        $response->assertSee('1'); // コメント数の表示を確認
    }

    /**
     * ログイン前のユーザーはコメントを送信できない [22: ID 9-2]
     */
    public function test_guest_user_cannot_send_comment()
    {
        $item = Item::factory()->create();

        $commentData = [
            'content' => '未ログインのコメント'
        ];

        // ログインせずに送信を試みる
        $response = $this->post("/item/{$item->id}/comment", $commentData);

        // 期待挙動：ログイン画面へリダイレクトされる [18: FN011-1]
        $response->assertRedirect('/login');
        
        // データベースに保存されていないこと
        $this->assertDatabaseMissing('comments', $commentData);
    }

    /**
     * コメントが入力されていない場合、バリデーションメッセージが表示される [22: ID 9-3]
     */
    public function test_comment_is_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'content' => ''
        ]);

        // 期待挙動：バリデーションエラーが発生すること [24: CommentRequest]
        $response->assertSessionHasErrors('content');
    }

    /**
     * コメントが255字以上の場合、バリデーションメッセージが表示される [22: ID 9-4]
     */
    public function test_comment_max_length()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'content' => str_repeat('あ', 256) // 256文字
        ]);

        // 期待挙動：バリデーションエラーが発生すること [24: CommentRequest]
        $response->assertSessionHasErrors('content');
    }
}
