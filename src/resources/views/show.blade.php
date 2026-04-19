@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<main class="product-detail-container">
    <div class="item-detail-wrapper">
    {{-- 左側：商品画像 [17: FN017-1] --}}
    <div class="item-image-container">
        @php
                // 外部URLかローカル保存かを判定 [23, 18: FN029]
                $imagePath = str_starts_with($item->image_url, 'http')
                    ? $item->image_url
                    : asset('storage/' . $item->image_url);
        @endphp
        <div class="image-wrapper">
                <img src="{{ $imagePath }}" alt="{{ $item->name }}">
        {{-- 購入済みならSoldを表示 [18: FN014-3, 22: ID 4] --}}
                @if($item->order()->exists())
                    <span class="sold-label"></span>
                @endif
        </div>
    </div>

    {{-- 右側：詳細情報 --}}
    <div class="item-info-container">
        <div class="main-header">
            <h1 class="item-name">{{ $item->name }}</h1>
            <p class="brand-name">{{ $item->brand }}</p>
            <p class="price">¥{{ number_format($item->price) }} <span class="tax-label">(税込)</span></p>

            {{-- いいね・コメント統計 [17: FN018, FN020] --}}
            <div class="stats-area">
                <div class="stat-group">
                    <form action="{{ route('like', $item->id) }}" method="POST" id="like-form">
                        @csrf
                        <button type="submit" class="stat-icon-btn{{ $item->isLikedBy(Auth::user()) ? 'is-liked' : '' }}">
                            @if($item->isLikedBy(Auth::user()))
                                <img src="{{ asset('img/ハートロゴ_ピンク.png') }}" alt="liked" class="stat-icon">
                            @else
                                <img src="{{ asset('img/ハートロゴ_デフォルト.png') }}" alt="like" class="stat-icon">
                            @endif
                        </button>
                    </form>
                    <span class="stat-count">{{ $item->likes_count }}</span>
                </div>
                <div class="stat-group">
                    <img src="{{ asset('img/ふきだしロゴ.png') }}" alt="comments" class="stat-icon">
                    <span class="stat-count">{{ $item->comments_count }}</span>
                </div>
            </div>
        </div>

        {{-- 購入ボタン [17: FN019, 21: ID 10] --}}
        <div class="action-area">
            @if($item->order)
                <button class="primary-btn sold-out" disabled>売り切れました</button>
            @else
                <a href="{{ route('purchase.create', $item->id) }}" class="primary-btn">購入手続きへ</a>
            @endif
        </div>

        {{-- 各セクション [17: FN017] --}}
        <section class="detail-section">
            <h2 class="section-title">商品説明</h2>
            <div class="section-content">
                <p class="description-text">{{ $item->description }}</p>
            </div>
        </section>

        <section class="detail-section">
            <h2 class="section-title">商品の情報</h2>
            <div class="section-content">
                <div class="info-row">
                    <span class="label">カテゴリー</span>
                    <div class="tag-list">
                        @foreach($item->categories as $category)
                            <span class="category-tag">{{ $category->name }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="info-row">
                    <span class="label">商品の状態</span>
                    <span class="value">{{ $item->condition->name }}</span>
                </div>
            </div>
        </section>

        <section class="detail-section">
            <h2 class="section-title">コメント({{ $item->comments_count }})</h2>
            <div class="section-content">
                @foreach($item->comments as $comment)
                    <div class="comment-item">
                        <div class="user-info">
                            <div class="avatar">
                                <img src="{{ $comment->user->profile?->image_url ? asset('storage/' . $comment->user->profile->image_url) : asset('img/user-default.png') }}" alt="プロフィール画像">
                            </div>
                            <span class="user-name">{{ $comment->user->name }}</span>
                        </div>
                        <div class="comment-bubble">
                            {{ $comment->content }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="detail-section comment-form">
            <h3 class="sub-title">商品へのコメント</h3>
            <form action="{{ route('comment.store', $item->id) }}" method="POST">
                @csrf
                <textarea name="content" class="comment-input">{{ old('content') }}</textarea>
                @error('content')
                    <p class="error-message" style="color:red;">{{ $message }}</p>
                @enderror

                <button type="submit" class="primary-btn">コメントを送信する</button>
            </form>
        </section>
    </div>
</div>
@endsection