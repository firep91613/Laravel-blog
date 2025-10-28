@extends('layouts.admin')
@section('title', 'Панель администратора')

@section('login')
    <div class="login__header">
        <h1 class="login__title">Вход в панель администратора</h1>
    </div>

    <div class="login__body">
        <form action="{{ route('admin.authenticate') }}" method="POST" class="form">
            @csrf

            <div class="form__group">
                <label for="email" class="form__label">E-mail:</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="form__input">

                @error('email')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="password" class="form__label">Пароль:</label>
                <input id="password" name="password" type="password" value="{{ old('password') }}" class="form__input">

                @error('password')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <input type="submit" value="Войти" class="form__submit">
            </div>
        </form>
    </div>
@endsection
