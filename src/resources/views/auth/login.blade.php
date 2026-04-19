@extends('layouts.simple')

@section('content')
{{-- login.cssの読み込み --}}
<link rel="stylesheet" href="{{ asset('css/login.css') }}">

<div class="login-container">
    <h1 class="login-title">ログイン</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- メールアドレス (FN008-1) --}}
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="text" id="email" name="email" value="{{ old('email') }}">
            {{-- FN010: 「メールアドレスを入力してください」等のエラー表示 --}}
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- パスワード (FN008-2) --}}
        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password">
            {{-- FN010: 「パスワードを入力してください」等のエラー表示 --}}
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- ログインボタン --}}
        <button type="submit" class="btn-login">ログインする</button>
    </form>

    {{-- 会員登録画面への遷移 (FN011-2) --}}
    <a href="{{ route('register') }}" class="register-link">会員登録はこちら</a>
</div>
@endsection