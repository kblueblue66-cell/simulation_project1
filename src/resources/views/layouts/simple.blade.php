<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtechフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/simple.css') }}">
</head>
<body>
    {{-- メール認証誘導画面 [1] に基づく、ロゴのみのヘッダー --}}
    <header class="simple-header">
        <div class="header-inner">
            <div class="header-logo">
                <a href="/">
                    <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
                </a>
            </div>
        </div>
    </header>

    <main class="simple-main">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </main>
</body>
</html>