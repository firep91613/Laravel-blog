@extends('layouts.admin')
@section('title', $post->title)

@section('content')
    <article class="post">
        <div class="post__header">
            <h1 class="post__title">{{ $post->title }}</h1>
            @if ($post->image)
                <div class="post__image">
                    <img src="{{ asset( config('custom.symbolic_images_link') . $post->image) }}" alt="">
                </div>
            @endif
        </div>

        <div class="post__content">{!! $post->content !!} </div>

        <div class="post__tags">
            <ul class="post__tags-list">
                @foreach($post->tags as $tag)
                    <li class="post__tag-item">
                        <span class="post__tag-link">{{ $tag->name }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="post__actions">
            <a href="{{ route('admin.posts.edit', $post) }}" class="link link_edit">Редактировать</a>|
            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="submit" value="Удалить" class="link-button link-button_delete">
            </form>
        </div>
    </article>
@endsection
