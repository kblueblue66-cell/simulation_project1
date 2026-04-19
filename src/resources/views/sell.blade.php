@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-container">
    <h1 class="page-title">商品の出品</h1>

    <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- 商品画像セクション [1, 17: FN029] -->
        <div class="form-section">
            <h2 class="section-label">商品画像</h2>
            <div class="image-upload-box">
                <label for="image_url" class="upload-label">
                    画像を選択する
                    <input type="file" name="image_url" id="image_url" class="hidden-input">
                </label>
            </div>
            @error('image_url') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <!-- 商品の詳細セクション [1, 17: FN028] -->
        <div class="form-section">
            <h2 class="section-title">商品の詳細</h2>

            <div class="input-group">
                <h3 class="section-label">カテゴリー</h3>
                <div class="category-grid">
                    @foreach($categories as $category)
                        <label class="category-tag">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}">
                            <span class="tag-label">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('categories') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div class="input-group">
                <h3 class="section-label">商品の状態</h3>
                <select name="condition_id" class="form-select">
                    <option value="">選択してください</option>
                    @foreach($conditions as $condition)
                        <option value="{{ $condition->id }}">{{ $condition->name }}</option>
                    @endforeach
                </select>
                @error('condition_id') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- 商品名と説明セクション [1] -->
        <div class="form-section">
            <h2 class="section-title">商品名と説明</h2>

            <div class="input-group">
                <h3 class="section-label">商品名</h3>
                <input type="text" name="name" class="form-input">
                @error('name') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div class="input-group">
                <h3 class="section-label">ブランド名</h3>
                <input type="text" name="brand" class="form-input">
                @error('brand') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div class="input-group">
                <h3 class="section-label">商品の説明</h3>
                <textarea name="description" rows="5" class="form-textarea"></textarea>
                @error('description') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- 販売価格セクション [1] -->
        <div class="form-section">
            <h2 class="section-title">販売価格</h2>
            <div class="input-group">
                <h3 class="section-label">販売価格</h3>
                <div class="price-input-wrapper">
                    <span class="currency-symbol">¥</span>
                    <input type="number" name="price" class="form-input price-input">
                </div>
                @error('price') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="submit-btn">出品する</button>
    </form>
</div>
@endsection