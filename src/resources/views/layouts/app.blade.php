<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtechフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header-inner">
            {{-- ロゴ: トップ画面へのリンク [5] --}}
            <div class="header-logo">
                <a href="{{ route('item.index') }}">
                    <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
                </a>
            </div>

            {{-- 検索バー: FN016 商品名で部分一致検索 [4] --}}
            <div class="header-search">
                <form action="{{ route('item.index') }}" method="GET">
                    <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
                </form>
            </div>

            {{-- ナビゲーション: ログイン状態で切り替え [3] --}}
            <nav class="header-nav">
                <ul class="nav-list">
                    @guest
                        {{-- 未ログイン時 [3] --}}
                        <li><a href="{{ route('login') }}">ログイン</a></li>
                    @endguest

                    @auth
                        {{-- ログイン時 [1, 2] --}}
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="logout-btn">ログアウト</button>
                            </form>
                        </li>
                    @endauth

                    {{-- マイページ [1-3] --}}
                    <li><a href="{{ route('mypage') }}">マイページ</a></li>

                    <li><a href="{{ route('item.create') }}" class="btn-sell">出品</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>