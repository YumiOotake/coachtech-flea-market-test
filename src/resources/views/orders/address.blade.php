@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endsection
@section('content')
    <div class="form__content">
        <div class="form__heading">
            <h1 class="form__heading-title">住所の変更</h1>
        </div>
        <form action="{{ route('orders.edit', ['item_id' => $item->id]) }}" method="POST" class="form">
            @csrf
            <div class="form__group">
                <div class="form__group-title">
                    <label for="postal_code" class="form__label">郵便番号</label>
                </div>
                <div class="form__group-content">
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}"
                        class="form__input">
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
                    <input type="text" id="address" name="address" class="form__input" value="{{ old('address') }}">
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
                    <input type="text" id="building" name="building" class="form__input" value="{{ old('building') }}">
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
