@extends('layouts.simple')

@section('content')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">

<div class="register-container">
    <h1 class="register-title">会員登録</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- ユーザー名 (FN002-1) --}}
        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- メールアドレス (FN002-2) --}}
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="text" id="email" name="email" value="{{ old('email') }}">
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- パスワード (FN002-3) --}}
        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password">
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- 確認用パスワード (FN002-4) --}}
        <div class="form-group">
            <label for="password_confirmation">確認用パスワード</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
            @error('password_confirmation')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- 登録ボタン --}}
        <button type="submit" class="btn-register">登録する</button>
    </form>

    {{-- ログイン画面への遷移 (FN005) --}}
    <a href="{{ route('login') }}" class="login-link">ログインはこちら</a>
</div>
@endsection