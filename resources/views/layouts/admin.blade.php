<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <meta charset="UTF-8">
</head>
<body>
    <div class="wrapper">
        <header class="header">
            <div class="header__logo">
                <span class="header__image">
                    <a href="/">
                        <img src="{{ config('custom.symbolic_images_link') . $adminLogo }}" alt="Логотип">
                    </a>
                </span>
            </div>
            @auth
                @if(auth()->user()->isAdmin())
                    <div class="header__profile">
                        <span class="avatar">
                            <a href="{{ route('admin.users.show', auth()->user()->id) }}" class="header__avatar">
                                <img src="{{ asset(config('custom.symbolic_images_link') . (auth()->user()->avatar ?? $defaultAvatar)) }}" alt="" class="avatar__image">
                            </a>
                        </span>

                        <form method="POST" action="{{ route('admin.logout') }}" class="">
                            @csrf
                            <input type="submit" value="Выйти" class="header__logout">
                        </form>
                    </div>
                @endif
            @endauth
        </header>

        <main class="main">
            @auth
                @if(auth()->user()->isAdmin())
                    @include('admin.sidebar')
                @endif
            @endauth

            <section class="content">
                @include('layouts.partials.flash')

                @auth
                    @yield(auth()->user()->isAdmin() ? 'content' : 'login')
                @endauth
                @guest
                    @yield('login')
                @endguest
            </section>

            <script src="{{ asset('/js/common.js') }}"></script>
        </main>

        <footer class="footer">
            <p>Мой блог</p>
        </footer>
    </div>
</body>
</html>
