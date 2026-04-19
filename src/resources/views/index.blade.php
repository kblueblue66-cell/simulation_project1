@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="container">
    {{-- タブメニュー: FN015 マイリスト切り替え --}}
    <nav class="tab-container">
        <ul class="tab-menu">
            <li class="tab-item {{ !request('tab') ? 'active' : '' }}">
                <a href="{{ route('item.index',['keyword' => request('keyword')]) }}" class="{{request('tab') != 'mylist' ? 'active' : ''}}">おすすめ</a>
            </li>
            <li class="tab-item {{ request('tab') == 'mylist' ? 'active' : '' }}">
                <a href="{{ route('item.index', ['tab' => 'mylist','keyword' => request('keyword')]) }}" class="{{ request('tab') == 'mylist' ? 'active' : '' }}">マイリスト</a>
            </li>
        </ul>
    </nav>

    {{-- 商品グリッド: ID 2 レスポンシブ対応 --}}
    <div class="product-grid">
        @foreach($items as $item)
            <div class="product-card">
                <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                    <div class="product-image-wrapper">
                        {{-- 商品画像: 商品データ一覧 [3] に基づくURL --}}
                        <img src="{{ asset('storage/' .$item->image_url) }}" alt="{{ $item->name }}">

                        {{-- Soldラベル: FN014 購入済み商品の表示 --}}
                        @if($item->isSold())
                            <div class="sold-label">Sold</div>
                        @endif
                    </div>
                    <span class="product-name">{{ $item->name }}</span>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
