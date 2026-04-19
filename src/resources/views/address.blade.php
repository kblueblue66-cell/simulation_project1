@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<main class="address-container">
    <h1>住所の変更</h1>
    <form action="{{ route('address.update', $item_id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="post_code">郵便番号</label>
            <input type="text" name="post_code" id="post_code" value="{{ old('post_code') }}">
            @error('post_code') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address') }}">
            @error('address') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building') }}">
        </div>

        <button type="submit" class="update-btn">更新する</button>
    </form>
</main>
@endsection
