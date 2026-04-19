@extends('layouts.simple')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify.css' )}}">
@endsection

@section('content')
<div class="verify-email">
    <h1 class="verify-email__title">メール認証のお願い</h1>

    <div class="verify-email__content">
        <p>登録いただいたメールアドレスに認証メールを送信しました。</p>
        <p>メール内のリンクをクリックして、会員登録を完了させてください。</p>
    </div>

    {{-- 認証メール再送機能 (FN013) --}}
    <form method="POST" action="{{ route('verification.send') }}" class="verify-email__form">
        @csrf
        <button type="submit" class="resend-btn">認証メールを再送する</button>
    </form>

    @if (session('status') == 'verification-link-sent')
            <p class="status-msg">新しい認証メールを送信しました。</p>
    @endif

    {{-- 認証画面への遷移ボタン (FN012-3) --}}
    <div class="verify-email__link">
        <a href="/email/verify" class="auth-link-btn">認証はこちらから</a>
    </div>
</div>
@endsection