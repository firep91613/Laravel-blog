@extends('layouts.public')

@section('title', 'Авторизация')

@section('content')
    <div class="auth">
        <div class="main-header">
            <h2 class="main-header__title">Авторизация</h2>
        </div>

        <form action="{{ route('public.auth.authenticate') }}" method="POST" class="form">
            @csrf

            <div class="form__group">
                <label for="email" class="form__label">Email:</label>
                <input type="email" id="email" name="email" class="form__input" required>

                @error('email')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="password" class="form__label">Пароль:</label>
                <input type="password" id="password" name="password" class="form__input" required>

                @error('password')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group form__group_remember">
                <input type="checkbox" id="remember_me" name="remember" class="form__checkbox">
                <label for="remember_me" class="form__label form__label_remember">Запомнить меня</label>

                @error('remember')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <input type="submit" value="Войти" class="form__submit">
                <a href="{{ route('password.request') }}" class="form__reset">Забыли пароль?</a>
            </div>

            <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
        </form>
    </div>
@endsection
