@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-container">
    <!-- ユーザー情報セクション [17: FN025] -->
    <div class="profile-header">
        <div class="profile-info">
            <div class="user-avatar">
                @if($user->profile && $user->profile->image_url)
                    <img src="{{ asset('storage/' . $user->profile->image_url) }}" alt="ユーザーアイコン">
                @else
                    <div class="avatar-placeholder"></div>
                @endif
            </div>
            <h1 class="user-name">{{ $user->name }}</h1>
        </div>
        <!-- プロフィール編集ボタン [17: FN026] -->
        <a href="{{ route('profile.edit') }}" class="edit-profile-btn">プロフィールを編集</a>
    </div>

    <!-- タブ切り替えセクション [15: PG11, PG12] -->
    <div class="tabs">
        <a href="{{ route('mypage', ['page' => 'sell']) }}" class="tab-item {{ $page  === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['page' => 'buy']) }}" class="tab-item {{ $page === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <!-- 商品一覧グリッド [12, 21: ID 13] -->
    <div class="item-grid">
    @forelse($items as $item)
        <div class="item-card">
            <a href="{{ route('item.show', $item->id) }}">
                <div class="item-image">
                    {{-- ポイント1: 画像パスの判定 [23, 18: FN029] --}}
                    @php
                        $imagePath = str_starts_with($item->image_url, 'http')
                            ? $item->image_url
                            : asset('storage/' . $item->image_url);
                    @endphp
                    <img src="{{ $imagePath }}" alt="{{ $item->name }}">

                    {{-- ポイント2: Soldラベルにテキストを追加 [18: FN014-3, 22: ID 4] --}}
                    @if($item->order()->exists())
                        <span class="sold-label">Sold</span>
                    @endif
                </div>
                <p class="item-name">{{ $item->name }}</p>
            </a>
        </div>
    @empty
        {{-- [22: ID 15] 未認証やデータなし時の表示 --}}
        <p class="no-items-msg">表示する商品がありません。</p>
    @endforelse
    </div>
</div>
@endsection
