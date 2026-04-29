@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/orders/create.css') }}">
@endsection
@section('content')
    <div class="order-confirm">
        <div class="order-confirm__left">
            <div class="order-confirm__group">
                <div class="order-confirm__item">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像" class="order-confirm__image">
                </div>
                <div class="order-confirm__item">
                    <h1 class="order-confirm__text">{{ $item->name }}</h1>
                    <p class="order-confirm__text">¥ {{ $item->price }}</p>
                </div>
            </div>
            {{-- <div class="order-confirm__group">
                <div class="order-confirm__item">
                    <h2 class="order-confirm__label">支払い方法</h2>
                    <select name="condition_id" class="form__select" id="condition">
                        <option value="">選択してください</option>
                        @foreach ($conditions as $condition)
                            <option
                                value="{{ $condition->id }}"{{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                                {{ $condition->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div> --}}
            <div class="order-confirm__group">
                <div class="order-confirm__item">
                    <div class="order-confirm__address">
                        <h2 class="order-confirm__label">配送先</h2>
                        <a href="{{ route('orders.edit', ['item_id' => $item->id]) }}"
                            class="order-confirm__link">変更する</a>
                    </div>
                    <p class="order-confirm__text">〒 {{ session('postal_code') ?? $user->profile->postal_code }}</p>
                    <p class="order-confirm__text">{{ session('address') ?? $user->profile->address }}</p>
                    <p class="order-confirm__text">{{ session('building') ?? $user->profile?->building }}</p>
                </div>
            </div>
        </div>

        <div class="order-confirm__right">
            <dl class="order-confirm__group">
                <div class="order-confirm__item">
                    <dt class="form__title">商品代金</dt>
                    <dd class="order-confirm__text">{{ $item->price }}</dd>
                </div>
                {{-- <div class="order-confirm__item">
                    <dt class="form__title">支払い方法</dt>
                    <dd class="order-confirm__text">{{ $order }}</dd>
                </div> --}}
            </dl>
            <form action="{{ route('orders.store', ['item_id' => $item->id]) }}" method="POST" class="order-confirm__form">
                @csrf
                <button type="submit" class="order-confirm__button">購入する</button>
            </form>
        </div>
    </div>
@endsection
