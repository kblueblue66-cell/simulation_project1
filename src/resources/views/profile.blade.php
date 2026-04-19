@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-setup">
    <h1 class="profile-setup__title">プロフィール設定</h1>

    <form action="/mypage/profile" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- 画像設定 --}}
        <div class="form-group--image">
            <div class="image-preview">
                {{-- 初期はデフォルト画像、または登録済みの画像を表示 --}}
                <img src="{{ $user->profile->image_url ?? asset('img/default-user.png') }}" alt="">
            </div>
            <label class="image-upload-btn">
                画像を選択する
                <input type="file" name="image" style="display:none;">
            </label>
            @error('image') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- ユーザー名 --}}
        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}">
            @error('name') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- 郵便番号 --}}
        <div class="form-group">
            <label for="post_code">郵便番号</label>
            <input type="text" name="post_code" id="post_code" value="{{ old('post_code', $user->profile->post_code ?? '') }}">
            @error('post_code') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- 住所 --}}
        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->profile->address ?? '') }}">
            @error('address') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- 建物名 --}}
        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building', $user->profile->building ?? '') }}">
        </div>

        <button type="submit" class="submit-btn">更新する</button>
    </form>
</div>
@endsection
