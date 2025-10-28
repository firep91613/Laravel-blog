@extends('layouts.admin')
@section('title', 'Добавление нового пользователя')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Добавление нового пользователя</h1>
    </div>

    <div class="content__body">
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="form">
            @csrf

            <div class="form__group">
                <label for="name" class="form__label">Имя пользователя:</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="form__input">

                @error('name')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="email" class="form__label">E-mail:</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="form__input">

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

                @error('image')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="role_id" class="form__label">Группа:</label>
                <select name="role_id" id="role_id" class="form__select">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>

                @error('role_id')
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
                <input type="submit" value="Создать" class="form__submit">
            </div>
        </form>
    </div>

    <script src="{{ asset('/js/image-changer.js') }}"></script>
    <script>
        (new ImageChanger(document.querySelector('.input-file__custom'), 45)).init()
    </script>
@endsection
