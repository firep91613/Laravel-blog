<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/public.css') }}">
</head>
<body class="page">
    <header class="header">
        <div class="header__wrapper">
            <h1 class="header__title">
                <a href="{{ route('public.posts.index') }}" class="header__link">{{ $siteTitle }}</a>
            </h1>
            <p class="header__subtitle">{{ $siteSubTitle }}</p>

            <div class="search">
                <form action="{{ route('public.search') }}" method="GET" class="search__form">
                    <input type="text" name="query" class="search__input" placeholder="Введите запрос для поиска">
                    <input type="submit" value="Поиск" class="button search__button">
                </form>
            </div>

            <div class="header__auth-buttons">
                @auth
                    <div class="header__greeting">
                        <a href="{{ route('public.profile.show', auth()->user()->id) }}">
                            <span class="avatar">
                                <img src="{{ asset(config('custom.symbolic_images_link') . (auth()->user()->avatar ?? $defaultAvatar)) }}" alt="" class="avatar__image">
                            </span>
                        </a>
                    </div>

                    <form action="{{ route('public.auth.logout') }}" method="POST" class="header__logout-form">
                        @csrf
                        <button type="submit" class="button button_logout">Выйти</button>
                    </form>
                @else
                    <a href="{{ route('public.auth.showForm') }}?redirect_to={{ urlencode(request()->fullUrl()) }}" class="button button_login">Авторизоваться</a>
                    <a href="{{ route('public.register.showForm') }}" class="button button_register">Зарегистрироваться</a>
                @endauth
            </div>

            <div class="header__post-create">
                @can('create', App\Models\Post::class)
                    <a href="{{ route('public.posts.create') }}" class="button button_create-post">Создать новый пост</a>
                @endcan
            </div>
        </div>
        @include('layouts.partials.flash')
    </header>

    <main class="main">
        @yield('content')

        <script src="{{ asset('/js/common.js') }}"></script>
    </main>

    <footer class="footer">
        <p class="footer__text">&copy; {{ date('Y') }} Блог о веб-разработке. Все права защищены.</p>
    </footer>
</body>
</html>

