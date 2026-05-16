@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/orders/create.css') }}">
@endsection
@section('content')
    <div class="order-confirm">
        <div class="order-confirm__left">
            <section class="order-confirm__group order-confirm__detail">
                <div class="order-confirm__item">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像" class="order-confirm__image">
                </div>
                <div class="order-confirm__item">
                    <h1 class="order-confirm__name">{{ $item->name }}</h1>
                    <p class="order-confirm__price">¥ {{ number_format($item->price) }}</p>
                </div>
            </section>
            <section class="order-confirm__group">
                <div class="order-confirm__item">
                    <h2 class="order-confirm__label">支払い方法</h2>
                    <form action="{{ route('orders.payment', ['item_id' => $item->id]) }}" method="POST">
                        @csrf
                        <select name="payment_method" onchange="this.form.submit()" class="order-confirm__select">
                            <option value="">選択してください</option>
                            <option value="0" {{ (string) session('payment_method') === '0' ? 'selected' : '' }}>
                                コンビニ支払い</option>
                            <option value="1" {{ (string) session('payment_method') === '1' ? 'selected' : '' }}>カード支払い
                            </option>
                        </select>
                    </form>
                    <div class="form__error">
                        @error('payment_method')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </section>
            <section class="order-confirm__group">
                <div class="order-confirm__item">
                    <div class="order-confirm__address-header">
                        <h2 class="order-confirm__label">配送先</h2>
                        <a href="{{ route('orders.edit', ['item_id' => $item->id]) }}" class="order-confirm__link">変更する</a>
                    </div>
                    <div class="order-confirm__address-body">
                        <p class="order-confirm__text">〒 {{ session('postal_code') ?? $user->profile->postal_code }}</p>
                        <p class="order-confirm__text">{{ session('address') ?? $user->profile->address }}</p>
                        <p class="order-confirm__text">{{ session('building') ?? $user->profile?->building }}</p>
                        <div class="form__error">
                            @error('postal_code')
                                {{ $message }}
                            @enderror
                            @error('address')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="order-confirm__right">
            <dl class="order-confirm__summary">
                <div class="order-confirm__summary-row">
                    <dt class="order-confirm__summary-label">商品代金</dt>
                    <dd class="order-confirm__text">{{ number_format($item->price) }}</dd>
                </div>
                <div class="order-confirm__summary-row">
                    <dt class="order-confirm__summary-label">支払い方法</dt>
                    <dd class="order-confirm__text">{{ $paymentLabel }}</dd>
                </div>
            </dl>
            <form action="{{ route('orders.store', ['item_id' => $item->id]) }}" method="POST"
                class="order-confirm__form">
                @csrf
                <input type="hidden" name="postal_code"
                    value="{{ session('postal_code') ?? $user->profile->postal_code }}">
                <input type="hidden" name="address" value="{{ session('address') ?? $user->profile->address }}">
                <input type="hidden" name="building" value="{{ session('building') ?? $user->profile?->building }}">
                <input type="hidden" name="payment_method" value="{{ session('payment_method') }}">
                <button type="submit" class="order-confirm__button">購入する</button>
            </form>
        </div>
    </div>
@endsection
