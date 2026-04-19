<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID 1: 名前が入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_name_is_required()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    /**
     * ID 1: メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_email_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * ID 1: パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_password_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * ID 1: パスワードが7文字以下の場合、バリデーションメッセージが表示される
     */
    public function test_password_length_check()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'pass7文字',
            'password_confirmation' => 'pass7文字',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /**
     * ID 1: パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
     */
    public function test_password_mismatch()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'mismatch_pass',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }

    /**
     * ID 1: 全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される
     */
    public function test_registration_success()
    {
        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        // データベースに登録されているか確認
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);

        // 会員登録後の遷移先を確認 [18: FN006, 22]
        // ※メール認証を実装している場合は、認証誘導画面（またはHOME）へリダイレクトされることを確認します
        $response->assertRedirect('/mypage/profile');
    }
    /**
 * ID 2: メールアドレスが入力されていない場合、バリデーションメッセージが表示される
 */
public function test_login_email_is_required()
{
    $response = $this->post('/login', [
        'email' => '',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
}

/**
 * ID 2: パスワードが入力されていない場合、バリデーションメッセージが表示される
 */
public function test_login_password_is_required()
{
    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => '',
    ]);

    $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
}

/**
 * ID 2: 入力情報が間違っている場合、バリデーションメッセージが表示される
 */
public function test_login_failed_with_incorrect_info()
{
    // 存在しないユーザー情報でログインを試行
    $response = $this->post('/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
}

/**
 * ID 2: 正しい情報が入力された場合、ログイン処理が実行される
 */
public function test_login_success()
{
    // テスト用のユーザーを作成
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    // ログインに成功し、認証されていることを確認
    $this->assertAuthenticatedAs($user);

    // ログイン後の遷移先を確認（基本設計書に基づきトップ画面等） [3]
    $response->assertRedirect('/');
}
public function test_logout_success()
{
    // 1. 準備：テスト用のユーザーを作成してログイン状態にする [22: ID 3-1]
    $user = User::factory()->create([
        'email' => 'logout-test@example.com',
        'password' => bcrypt('password123'),
    ]);

    // ログイン状態をシミュレート
    $this->actingAs($user);

    // 2. 実行：ログアウトボタン（エンドポイント）を押下 [18: FN013, 22: ID 3-2]
    // Fortifyの標準仕様に基づき、POSTメソッドで /logout を呼び出します
    $response = $this->post('/logout');

    // 3. 検証：ログアウト処理が実行され、未認証状態になっていること [22: ID 3 期待挙動]
    $this->assertGuest();

    // 4. 検証：ログアウト後のリダイレクト先（通常はトップ画面 / ）を確認
    $response->assertRedirect('/');
}
}
