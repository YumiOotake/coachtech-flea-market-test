@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mypage/edit.css') }}">
@endsection
@section('content')
    <div class="form__content">
        <div class="form__heading">
            <h1 class="form__heading-title">プロフィール設定</h1>
        </div>
        <form action="{{ route('mypage.update') }}" method="POST" class="form" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="form__group">
                <div class="form__profile">
                    <div class="form__image">
                        @if ($user->profile?->profile_image)
                            <img src="{{ asset('storage/' . $user->profile->profile_image) }}" alt="プロフィール画像"
                                class="form__img">
                        @else
                            <div class="form__img form__img--placeholder"></div>
                        @endif
                    </div>
                    <label for="image" class="form__file-btn">画像を選択する</label>
                    <input type="file" name="image" id="image" class="form__input--image">
                </div>
                <div class="form__error">
                    @error('image')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label for="name" class="form__label">ユーザー名</label>
                </div>
                <div class="form__group-content">
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        class="form__input">
                </div>
                <div class="form__error">
                    @error('name')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label for="postal_code" class="form__label">郵便番号</label>
                </div>
                <div class="form__group-content">
                    <input type="text" id="postal_code" name="postal_code"
                        value="{{ old('postal_code', $user->profile?->postal_code) }}" class="form__input">
                </div>
                <div class="form__error">
                    @error('postal_code')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label for="address" class="form__label">住所</label>
                </div>
                <div class="form__group-content">
                    <input type="text" id="address" name="address"
                        value="{{ old('address', $user->profile?->address) }}" class="form__input">
                </div>
                <div class="form__error">
                    @error('address')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <label for="building" class="form__label">建物名</label>
                </div>
                <div class="form__group-content">
                    <input type="text" id="building" name="building"
                        value="{{ old('building', $user->profile?->building) }}" class="form__input">
                </div>
                <div class="form__error">
                    @error('building')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="form__button">
                <button class="form__button-submit" type="submit">更新する</button>
            </div>
        </form>
    </div>
@endsection
