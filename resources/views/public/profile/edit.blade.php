@extends('layouts.public')

@section('content')
    <div class="profile">
        <div class="main-header">
            <h2 class="main-header__title">Редактирование профиля</h2>
        </div>

        <form method="POST" action="{{ route('public.profile.update', $user->id) }}" enctype="multipart/form-data" class="form">
            @csrf
            @method('PUT')

            <div class="form__group">
                <label for="name" class="form__label">Имя:</label>
                <input type="text" name="name" id="name" value="{{ $user->name }}" class="form__input" required>

                @error('name')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="email" class="form__label">Email:</label>
                <input type="email" name="email" id="email" value="{{ $user->email }}" class="form__input" required>

                @error('email')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="input-file">
                    <span type="text" class="input-file__text"></span>
                    <input type="file" name="avatar" class="input-file__custom" data-old="{{ config('custom.symbolic_images_link') . ($user->avatar ?? $defaultAvatar) }}">
                    <span class="input-file__btn">Выберите изображение</span>
                </label>

                @error('avatar')
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
                <label for="password_confirmation" class="form__label">Подтверждение пароля:</label>
                <input id="password_confirmation" name="password_confirmation" type="password" value="{{ old('password_confirmation') }}" class="form__input">

                @error('password_confirmation')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <input type="submit" value="Сохранить изменения" class="form__submit">
            </div>
        </form>
    </div>

    <script src="{{ asset('/js/image-changer.js') }}"></script>
    <script>
        (new ImageChanger(document.querySelector('.input-file__custom'), 45)).init()
    </script>
@endsection

