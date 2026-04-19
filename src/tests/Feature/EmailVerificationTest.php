<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 会員登録後、認証メールが送信される [22: ID 16-1]
     */
    public function test_verification_email_is_sent_after_registration()
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 登録したユーザー宛に認証メールが送信されたか確認 [1]
        $user = User::where('email', 'test@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * 未認証の状態でログインを試みると、メール認証誘導画面へ遷移する [18: FN012-2]
     */
    public function test_unverified_user_is_redirected_to_verification_notice()
    {
        // 認証が終わっていないユーザー
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // ログイン後にメール認証誘導画面（PG04関連）へリダイレクトされるか
        $response = $this->actingAs($user)->get('/mypage');

        // Laravel標準（Fortify）の場合、verification.noticeへ送られる
        $response->assertRedirect('/email/verify');
    }

    /**
     * メール認証を完了すると、プロフィール設定画面に遷移する [22: ID 16-3]
     */
    public function test_user_can_verify_email_and_redirect_to_profile_settings()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // 認証URLを生成
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        // プロフィール設定画面（/mypage/profile）へ遷移するか検証 [18: FN012-4-d]
        $response->assertRedirect('/mypage/profile?verified=1');

        // データベース上の認証日時が更新されているか検証
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    /**
     * 認証メールを再送できる [18: FN013]
     */
    public function test_user_can_resend_verification_email()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // 再送ルートにPOSTリクエストを送る
        $this->actingAs($user)->post('/email/verification-notification');

        // 再び認証メールが送信されたことを検証
        Notification::assertSentTo($user, VerifyEmail::class);

}
}