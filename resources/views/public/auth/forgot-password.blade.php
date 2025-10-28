@extends('layouts.public')

@section('title', 'Сброс пароля')

@section('content')
    <div class="auth">
        <div class="main-header">
            <h2 class="main-header__title">Сброс пароля</h2>
        </div>

        <form action="{{ route('password.email') }}" method="POST" class="form">
            @csrf

            <div class="form__group">
                <label for="email" class="form__label">Email:</label>
                <input type="email" id="email" name="email" class="form__input" required>

                @error('email')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <input type="submit" value="Отправить" class="form__submit">
            </div>
        </form>
    </div>
@endsection
