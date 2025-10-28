@extends('layouts.public')

@section('title', 'Сброс пароля')

@section('content')
    <div class="auth">
        <div class="main-header">
            <h2 class="main-header__title">Сброс пароля</h2>
        </div>

        <form action="{{ route('password.update') }}" method="POST" class="form">
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

            <div class="form__group">
                <label for="password_confirmation" class="form__label">Подтверждение пароля:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="form__input">

                @error('password_confirmation')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form__group">
                <input type="submit" value="Отправить" class="form__submit">
            </div>
        </form>
    </div>
@endsection

