@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/item.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mypage/index.css') }}">
@endsection
@section('content')
    <section class="mypage__content">
        <div class="mypage__profile">
            @if ($user->profile?->profile_image)
                <img src="{{ asset('storage/' . $user->profile?->profile_image) }}" alt="プロフィール画像" class="mypage__user-icon">
            @else
                <div class="mypage__user-icon mypage__user-icon--placeholder"></div>
            @endif
            <p class="mypage__user-name">{{ $user->name }}</p>
        </div>
        <div class="mypage__button">
            <a href="{{ route('mypage.edit') }}" class="mypage__edit-link">プロフィールを編集</a>
        </div>
    </section>
    <div class="item__content">
        <div class="item__heading">
            <a href="{{ route('mypage.index', ['page' => 'sell']) }}"
                class="item__tab {{ request('page') === 'sell' ? 'item__tab--active' : '' }}">出品した商品</a>
            <a href="{{ route('mypage.index', ['page' => 'buy']) }}"
                class="item__tab {{ request('page') === 'buy' ? 'item__tab--active' : '' }}">購入した商品</a>
        </div>
        @forelse ($items as $item)
            <article class="item-card">
                <figure class="item-card__figure">
                    <div class="item-card__image">
                        <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像" class="item-card__img">
                    </div>
                    <figcaption class="item-card__figcaption">
                        <p class="item-card__figcaption--title">{{ $item->name }}</p>
                    </figcaption>
                </figure>
            </article>
        @empty
            <p class="item__empty">商品が見つかりませんでした</p>
        @endforelse
    </div>
@endsection
