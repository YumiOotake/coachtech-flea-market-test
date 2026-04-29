@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endsection
@section('content')
    <div class="form__content">
        <div class="form__heading">
            <h1 class="form__heading-title">商品の出品</h1>
        </div>
        <form action="{{ route('items.store') }}" method="POST" class="form" enctype="multipart/form-data">
            @csrf
            <div class="form__group">
                <div class="form__group-title">
                    <label for="image" class="form__label">商品画像</label>
                </div>
                <div class="form__group-content">
                    <input type="file" id="image" name="image" value="{{ old('image') }}" class="form__input">
                </div>
                <div class="form__error">
                    @error('image')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <h2 class="form__section-title">商品の詳細</h2>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label for="category" class="form__label">カテゴリー</label>
                </div>
                <div class="form__group-content">
                    @foreach ($categories as $category)
                        <label class="form__category-btn">
                            <input type="checkbox" name="category_id[]" value="{{ $category->id }}"
                                class="form__input-checkbox">
                            <span>{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="form__error">
                    @error('category_id')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label for="condition" class="form__label">商品の状態</label>
                </div>
                <div class="form__group-content">
                    <select name="condition_id" class="form__select" id="condition">
                        <option value="">選択してください</option>
                        @foreach ($conditions as $condition)
                            <option
                                value="{{ $condition->id }}"{{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                                {{ $condition->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form__error">
                    @error('condition_id')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <h2 class="form__section-title">商品名と説明</h2>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label for="name" class="form__label">商品名</label>
                </div>
                <div class="form__group-content">
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="form__input">
                </div>
                <div class="form__error">
                    @error('name')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label for="brand" class="form__label">ブランド名</label>
                </div>
                <div class="form__group-content">
                    <input type="text" id="brand" name="brand" value="{{ old('brand') }}" class="form__input">
                </div>
                <div class="form__error">
                    @error('brand')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label for="description" class="form__label">商品の説明</label>
                </div>
                <div class="form__group-content">
                    <textarea name="description" id="description" cols="30" rows="10" class="form__textarea">{{ old('description') }}</textarea>
                </div>
                <div class="form__error">
                    @error('description')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label for="price" class="form__label">販売価格</label>
                </div>
                <div class="form__group-content">
                    <input type="text" id="price" name="price" value="{{ old('price') }}" class="form__input"
                        placeholder="¥">
                </div>
                <div class="form__error">
                    @error('price')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">出品する</button>
            </div>
        </form>
    </div>
@endsection
