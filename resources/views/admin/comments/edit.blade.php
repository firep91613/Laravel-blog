@extends('layouts.admin')
@section('title', 'Комментарий №' . $comment->id)

@section('content')
    <div class="content__header">
        <h1 class="content__title">Комментарий №{{ $comment->id }}</h1>
    </div>

    <div class="content__body">
        <form action="{{ route('admin.comments.update', $comment) }}" method="POST" class="form">
            @csrf
            @method('PUT')

            <div class="form__group">
                <label for="content" class="form__label">Содержание:</label>
                <textarea name="content" id="content" cols="50" rows="5" class="form__textarea">{{ old('content', $comment->content) }}</textarea>

                @error('content')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <input type="hidden" name="user_id" value="{{ $comment->user->id }}">
            <input type="hidden" name="post_id" value="{{ $comment->post_id }}">
            <input type="hidden" name="parent_id" value="{{ $comment->parent_id }}">

            <div class="form__group">
                <input type="submit" value="Сохранить" class="form__submit">
            </div>
        </form>
    </div>
@endsection
