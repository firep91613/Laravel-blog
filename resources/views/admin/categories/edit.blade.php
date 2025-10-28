@extends('layouts.admin')
@section('title', 'Редактирование категории ' . $category->name)

@section('content')
    <div class="content__header">
        <h1 class="content__title">Редактирование категории "{{ $category->name }}"</h1>
    </div>

    <div class="content__body">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="form">
            @csrf
            @method('PUT')

            <div class="form__group">
                <label for="name" class="form__label">Название категории:</label>
                <input id="name" name="name" type="text" value="{{ old('name', $category->name) }}" class="form__input">

                @error('name')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="description" class="form__label">Описание категории:</label>
                <input id="description" name="description" type="text" value="{{ old('description', $category->description) }}" class="form__input">

                @error('description')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="slug" class="form__label">Слаг:</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug', $category->slug) }}" class="form__input">

                @error('slug')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <input type="submit" value="Сохранить" class="form__submit">
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
