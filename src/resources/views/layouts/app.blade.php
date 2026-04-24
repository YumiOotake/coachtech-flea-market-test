<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>flea market</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <a href="{{ route('items.index') }}" class="header__logo-link">
                    <img src="{{ asset('storage/' . 'images/COACHTECHヘッダーロゴ .png') }}" alt="ヘッダーロゴ画像"
                        class="header__logo-img">
                </a>
            </div>
            @if (!Request::is('login') && !Request::is('register'))
                <form class="search-form" action="{{ route('items.index') }}" method="get">
                    <div class="search-form__item">
                        <input type="text" name="keyword" class="search-form__item-input" placeholder="なにをお探しですか？"
                            value="{{ request('keyword') }}">
                    </div>
                </form>
                <nav class="header__nav">
                    @guest
                        <div class="header__nav-item">
                            <a href="{{ route('login') }}" class="header__nav-link">ログイン</a>
                        </div>
                    @endguest
                    @auth
                        <form action="{{ route('logout') }}" method="post" class="header__nav-item">
                            @csrf
                            <button type="submit" class="header__nav-link">ログアウト</button>
                        </form>
                    @endauth
                    <div class="header__nav-item">
                        <a href="{{ route('mypage.index') }}" class="header__nav-link">マイページ</a>
                    </div>
                    <div class="header__nav-item">
                        <a href="{{ route('items.create') }}" class="header__nav-create">出品</a>
                    </div>
                </nav>
            @endif
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>

</html>
