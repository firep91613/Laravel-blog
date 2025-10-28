@extends('layouts.public')

@section('title', 'Регистрация')

@section('content')
    <div class="registration">
        <div class="main-header">
            <h2 class="main-header__title">Регистрация</h2>
        </div>

        <form action="{{ route('public.register.register') }}" method="POST" enctype="multipart/form-data" class="form">
            @csrf

            <div class="form__group">
                <label for="name" class="form__label">Имя:</label>
                <input type="text" id="name" name="name" class="form__input" required>

                @error('name')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="email" class="form__label">Email:</label>
                <input type="email" id="email" name="email" class="form__input" required>

                @error('email')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="input-file">
                    <span type="text" class="input-file__text"></span>
                    <input type="file" name="avatar" class="input-file__custom">
                    <span class="input-file__btn">Выберите изображение</span>
                </label>

                @error('avatar')
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
                <input type="password" id="password_confirmation" name="password_confirmation" class="form__input" required>

                @error('password_confirmation')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <input type="submit" value="Зарегистрироваться" class="form__submit">
            </div>
        </form>
    </div>

    <script src="{{ asset('/js/image-changer.js') }}"></script>
    <script>
        (new ImageChanger(document.querySelector('.input-file__custom'), 45)).init()
    </script>
@endsection

