@extends('layouts.admin')
@section('title', 'Добавление новой категории')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Создать новую категорию</h1>
    </div>

    <div class="content__body">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="form">
            @csrf

            <div class="form__group">
                <label for="name" class="form__label">Название категории:</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="form__input">
                @error('name')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="slug" class="form__label">Слаг:</label>
                <input id="slug" name="slug" type="text" value="{{ old('description') }}" class="form__input">
                @error('slug')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="description" class="form__label">Описание категории:</label>
                <input id="description" name="description" type="text" value="{{ old('description') }}" class="form__input">
                @error('description')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <input type="submit" value="Создать" class="form__submit">
            </div>
        </form>
    </div>

    <script src="{{ asset('/js/slug.js') }}"></script>
    <script>
        (new SlugGenerator(
            document.getElementById('name'),
            document.getElementById('slug')
        )).init();
    </script>
@endsection
