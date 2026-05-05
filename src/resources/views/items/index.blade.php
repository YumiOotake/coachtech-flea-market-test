@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection
@section('content')
    <div class="item__content">
        <div class="item__heading">
            <a href="{{ route('items.index') }}"
                class="item__tab {{ request('tab') !== 'mylist' ? 'item__tab--active' : '' }}">おすすめ</a>
            <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}"
                class="item__tab {{ request('tab') === 'mylist' ? 'item__tab--active' : '' }}">マイリスト</a>
        </div>
        <div class="item__list">
        @forelse ($items as $item)
            <article class="item-card">
                <a href="{{ route('items.show', ['item_id' => $item->id]) }}" class="item-card__link">
                    <figure class="item-card__figure">
                        <div class="item-card__image">
                            <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像" class="item-card__img">
                            @if ($item->order()->exists())
                                <p class="item-card__sold">Sold</p>
                            @endif
                        </div>
                        <figcaption class="item-card__figcaption">
                            <p class="item-card__title">{{ $item->name }}</p>
                        </figcaption>
                    </figure>
                </a>
            </article>
        @empty
            <p class="item__empty">商品はありません</p>
        @endforelse
        </div>
    </div>
@endsection
