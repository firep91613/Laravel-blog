@extends('layouts.public')
@section('title', 'Добавление нового поста')

@section('content')
    <div class="post-create">
        <div class="main-header">
            <h2 class="main-header__title">Создать новый пост</h2>
        </div>

        <form action="{{ route('public.posts.store') }}" method="POST" enctype="multipart/form-data" class="form">
            @csrf

            <div class="form__group">
                <label for="title" class="form__label">Заголовок:</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="form__input">

                @error('title')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="slug" class="form__label">Слаг:</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="form__input">

                @error('slug')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="excerpt" class="form__label">Краткое содержание:</label>
                <textarea name="excerpt" id="excerpt" cols="50" rows="5" class="form__textarea">{{ old('excerpt') }}</textarea>

                @error('excerpt')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="content" class="form__label">Содержание:</label>
                <textarea name="content" id="content" cols="50" rows="10" class="form__textarea">{{ old('content') }}</textarea>

                @error('content')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="input-file">
                    <span type="text" class="input-file__text"></span>
                    <input type="file" name="image" class="input-file__custom">
                    <span class="input-file__btn">Выберите изображение</span>
                </label>

                @error('image')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <input type="hidden" name="user_id" value="{{ auth()->check() ? auth()->user()->id : '' }}">

            <div class="form__group">
                <label for="category_id" class="form__label">Категория:</label>
                <select name="category_id" id="category_id" class="form__select">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                @error('category_id')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label for="tag_id" class="form__label">Теги:</label>

                <select name="tag_id[]" id="tag_id" class="form__select form__select_hidden" multiple="multiple">
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                    @endforeach
                </select>

                @error('tag_id')
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
        (new ImageChanger(
            document.querySelector('.input-file__custom'),
            100
        )).init();
    </script>

    <script src="{{ asset('/js/tag.js') }}"></script>
    <script>
        (new CustomSelect(document.getElementById('tag_id'))).init();
    </script>

    <script src="{{ asset('/js/slug.js') }}"></script>
    <script>
        (new SlugGenerator(
            document.getElementById('title'),
            document.getElementById('slug')
        )).init();
    </script>

    <script src="https://cdn.ckeditor.com/ckeditor5/30.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#excerpt'))
            .catch(error => {
                console.error(error);
            });

        ClassicEditor
            .create(document.querySelector('#content'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endsection
