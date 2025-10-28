@extends('layouts.public')

@section('content')
    <div class="profile">
        <div class="profile__header">
            <h2 class="profile__title">Личный кабинет</h2>
            <p class="profile__subtitle">Просмотр и редактирование вашего профиля</p>
        </div>

        <div class="avatar avatar_profile">
            <img src="{{ asset(config('custom.symbolic_images_link') . ($user->avatar ?? $defaultAvatar)) }}" alt="" class="avatar__image">
        </div>

        <div class="profile__details">
            <p class="profile__label"><strong>Имя:</strong> {{ $user->name }}</p>
            <p class="profile__label"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="profile__label"><strong>Дата регистрации:</strong> {{ $user->created_at->format('d-m-Y') }}</p>
        </div>

        <div class="profile__actions">
            <a href="{{ route('public.profile.edit', $user->id) }}" class="button button_edit">Редактировать профиль</a>
        </div>
    </div>
@endsection
