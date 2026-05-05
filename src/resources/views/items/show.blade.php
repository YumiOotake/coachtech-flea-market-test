@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection
@section('content')
    <div class="show__content">
        <div class="item-show__left">
            <section class="item-show__section">
                <div class="item-show__image">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像" class="item-show__img">
                </div>
            </section>
        </div>
        <div class="item-show__right">
            <section class="item-show__section">
                <h1 class="item-show__name">{{ $item->name }}</h1>
                <p class="item-show__brand">{{ $item->brand }}</p>
                <div class="item-show__group">
                    <span class="item-show__price-unit">¥</span>
                    <p class="item-show__price">{{ number_format($item->price) }}</p>
                    <span class="item-show__price-tax">&lpar;税込&rpar;</span>
                </div>
                <div class="item-show__icon">
                    <div class="item-show__like">
                        @if ($item->likedBy->contains(auth()->id()))
                            <form action="{{ route('like.destroy', ['item_id' => $item->id]) }}" method="POST"
                                class="item-show__like-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="item-show__remove-like">
                                    <img src="{{ asset('storage/' . 'images/ハートロゴ_ピンク.png') }}" alt="ハートロゴピンク画像"
                                        class="item-show__like-img">
                                </button>
                            </form>
                            <span class="item-show__like-count">{{ $item->likedBy->count() }}</span>
                        @else
                            <form action="{{ route('like.store', ['item_id' => $item->id]) }}" method="POST"
                                class="item-show__like-form">
                                @csrf
                                <button type="submit" class="item-show__add-like">
                                    <img src="{{ asset('storage/' . 'images/ハートロゴ_デフォルト.png') }}" alt="ハートロゴデフォルト画像"
                                        class="item-show__like-img">
                                </button>
                            </form>
                            <span class="item-show__like-count">{{ $item->likedBy->count() }}</span>
                        @endif
                    </div>
                    <div class="item-show__comment">
                        <img src="{{ asset('storage/' . 'images/ふきだしロゴ .png') }}" alt="コメント画像"
                            class="item-show__comment-img">
                        <span class="item-show__comment-count">{{ $item->comments()->count() }}</span>
                    </div>
                </div>
            </section>
            <div class="item-show__section item-show__button">
                <a href="{{ route('orders.create', ['item_id' => $item->id]) }}" class="item-show__button--purchase">
                    購入手続きへ
                </a>
            </div>
            <section class="item-show__section">
                <h2 class="item-show__title">商品説明</h2>
                <p class="item-show__description">{{ $item->description }}</p>
            </section>
            <section class="item-show__section">
                <h2 class="item-show__title">商品の情報</h2>
                <div class="item-show__group item-show__group--category">
                    <span class="item-show__sub-title">カテゴリー</span>
                    @foreach ($item->categories as $category)
                        <p class="item-show__category">{{ $category->name }}</p>
                    @endforeach
                </div>
                <div class="item-show__group">
                    <span class="item-show__sub-title">商品の状態</span>
                    <p class="item-show__condition">{{ $item->condition->name }}</p>
                </div>
            </section>
            <section class="item-show__section">
                <h2 class="item-show__title item-show__comment-title">コメント&lpar;{{ $item->comments()->count() }}&rpar;</h2>
                @if ($item->comments()->count() > 0)
                    @foreach ($item->comments as $comment)
                        <div class="item-show__comment-block">
                            <div class="item-show__profile-group">
                                @if ($comment->user->profile?->profile_image)
                                    <img src="{{ asset('storage/' . $comment->user->profile?->profile_image) }}"
                                        alt="プロフィール画像" class="item-show__profile-image">
                                @else
                                    <div class="item-show__profile-image item-show__profile-image--placeholder"></div>
                                @endif
                                <p class="item-show__user-name">{{ $comment->user->name }}</p>
                            </div>
                            <p class="item-show__content">{{ $comment->content }}</p>
                        </div>
                    @endforeach
                @endif
                <form action="{{ route('comment.store', $item) }}" method="POST" class="item-show__form">
                    @csrf
                    <div class="item-show__title">
                        <label for="content" class="item-show__comment-label">商品へのコメント</label>
                    </div>
                    <div class="item-show__text">
                        <textarea id="content" name="content" cols="30" rows="10" class="item-show__textarea"></textarea>
                    </div>
                    <div class="item-show__error">
                        @error('content')
                            {{ $message }}
                        @enderror
                    </div>
                    <button type="submit" class="item-show__add-comment">
                        コメントを送信する
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
