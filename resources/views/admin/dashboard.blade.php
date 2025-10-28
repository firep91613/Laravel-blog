@extends('layouts.admin')
@section('title', 'Панель администратора')

@section('content')
    <div class="main__header">
        <h1 class="main__title">Панель администратора</h1>
    </div>

    <section class="stats">
        <h2 class="stats__title">Статистика</h2>
        <p class="stats__item">{{ 'Количество пользователей: ' . $usersCount }}</p>
        <p class="stats__item">{{ 'Количество постов: ' . $postsCount }}</p>
    </section>

    <section class="latest-users">
        <h2 class="latest-users__title">Последние зарегистрированные пользователи:</h2>
        <ul class="latest-users__list">
            @foreach($latestUsers as $user)
                <li class="latest-users__item">
                    <a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a>
                </li>
            @endforeach
        </ul>
    </section>

    <section class="latest-comments">
        <h2 class="latest-comments__title">Последние комментарии:</h2>
        <ul class="latest-comments__list">
            @foreach($latestComments as $comment)
                <li class="latest-comments__item">
                    <a href="{{ route('admin.comments.index') }}">{{ $comment->content }}</a>
                </li>
            @endforeach
        </ul>
    </section>
@endsection
