@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection
@section('content')
    <div class="show__content">

        <div class="item-show">
            <section class="item-show__item">
                <div class="item-show__image">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像" class="item-show__img">
                </div>
                <div class="item-show__name">
                    <h1 class="item-show__text">{{ $item->name }}</h1>
                    <p class="item-show__text">{{ $item->brand }}</p>
                </div>
                <div class="item-show__price">
                    <span class="item-show__text--dollar">¥</span>
                    <p class="item-show__text">{{ $item->price }}</p>
                    <span class="item-show__text--tax">¥</span>
                </div>
            </section>
            <section class="item-show__item">
                <div class="item-show__like">
                    @if ($item->likedBy->contains(auth()->id()))
                        <form action="{{ route('like.destroy', $item) }}" method="POST" class="item-show__like-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="item-show__remove-like">
                                <img src="{{ asset('storage/' . 'images/ハートロゴ_ピンク.png') }}" alt="ハートロゴピンク画像">
                            </button>
                        </form>
                        <span class="item-show__like-count">{{ $item->likedBy->count() }}</span>
                    @else
                        <form action="{{ route('like.store', $item) }}" method="POST" class="item-show__like-form">
                            @csrf
                            <button type="submit" class="item-show__add-like">
                                <img src="{{ asset('storage/' . 'images/ハートロゴ_デフォルト.png') }}" alt="ハートロゴデフォルト画像">
                            </button>
                        </form>
                        <span class="item-show__like-count">{{ $item->likedBy->count() }}</span>
                    @endif
                </div>
            </section>
            <div class="item-show__item">
                <a href="{{ route('orders.create', $item) }}" class="item-show__button--purchase">
                    購入手続きへ
                </a>
            </div>
            <section class="item-show__item">
                <h2 class="item-show__title">商品説明</h2>
                <p class="item-show__text">{{ $item->description }}</p>
            </section>
            <section class="item-show__item">
                <h2 class="item-show__title">商品の情報</h2>
                <div class="item-show__group">
                    <span class="item-show__sub-title">カテゴリー</span>
                    @foreach ($item->categories as $category)
                        <p class="item-show__text">{{ $category->name }}</p>
                    @endforeach
                </div>
                <div class="item-show__group">
                    <span class="item-show__sub-title">商品の状態</span>
                    <p class="item-show__text">{{ $item->condition->name }}</p>
                </div>
            </section>
            <section class="item-show__item">
                <h2 class="item-show__title">コメント&lpar;{{ $item->comments()->count() }}&rpar;</h2>
                @if ($item->comments()->count() > 0)
                    @foreach ($item->comments as $comment)
                        <div class="item-show__group">
                            <img src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="商品画像"
                                class="item-show__user-icon">
                            <p class="item-show__user-name">{{ $comment->user->name }}</p>
                        </div>
                        <div class="item-show__group">
                            <p class="item-show__user-name">{{ $comment->content }}</p>
                        </div>
                    @endforeach
                @else
                    <p class="item-show__empty">コメントがありません</p>
                @endif
                <form action="{{ route('comment.store', $item) }}" method="POST" class="item-show__form">
                    @csrf
                    <textarea name="content" cols="30" rows="10" class="item-show__textarea"></textarea>
                    <button type="submit" class="item-show__add-comment">
                        コメントを送信する
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
