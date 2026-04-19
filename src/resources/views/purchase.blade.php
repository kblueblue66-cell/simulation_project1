@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<main class="purchase-container">
    <div class="purchase-main">
        {{-- FN021: 商品情報 --}}
        <div class="item-summary">
            <div class="item-image">
                <img src="{{ ($item->image_url) }}" alt="{{ $item->name }}">
            </div>
            <div class="item-details">
                <h1>{{ $item->name }}</h1>
                <p class="price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>

        {{-- FN023: 支払い方法選択 --}}
        <div class="selection-section">
            <h2>支払い方法</h2>
            <select name="payment_method" form="purchase-form" id="payment-select">
                <option value="" disabled selected>選択してください</option>
                <option value="konbini">コンビニ払い</option>
                <option value="card">カード支払い</option>
            </select>
        </div>

        {{-- FN024: 配送先 --}}
        <div class="selection-section">
            <div class="section-header">
                <h2>配送先</h2>
                <a href="{{ route('address.edit', $item->id) }}" class="change-link">変更する</a>
            </div>
            <div class="address-display">
                <p>〒 {{ $address->post_code }}</p>
                <p>{{ $address->address }} {{ $address->building}}</p>
            </div>
        </div>
    </div>

    {{-- 右側：確認画面エリア --}}
    <aside class="purchase-confirm">
        <table class="confirm-table">
            <tr>
                <th>商品代金</th>
                <td>¥{{ number_format($item->price) }}</td>
            </tr>
            <tr>
                <th>支払い方法</th>
                <td id="selected-payment">
                    @if($payment_method === 'card') カード支払い
                @elseif($payment_method === 'konbini') コンビニ払い
                @endif
                </td>
            </tr>
        </table>

        {{-- FN022: 購入アクション --}}
        <form action="{{ route('purchase.store', $item->id) }}" method="POST" id="purchase-form">
            @csrf
            <input type="hidden" name="post_code" value="{{ $user->profile->post_code }}">
            <input type="hidden" name="address" value="{{ $user->profile->address  }}">
            <input type="hidden" name="building" value="{{ $user->profile->building }}">
            <button type="submit" class="purchase-btn">購入する</button>
        </form>
    </aside>
</main>

<script>
    document.getElementById('payment-select').addEventListener('change', function() {
        const text = this.options[this.selectedIndex].text;
        document.getElementById('selected-payment').innerText = text;
    });
</script>
@endsection
