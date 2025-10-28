@extends('layouts.public')
@section('title', $post->title)

@section('content')
    <section class="post">
        <article class="post__content">
            <div class="post__header">
                <h1 class="post__title">{{ $post->title }}</h1>
                @if ($post->image)
                    <p class="post__preview">
                        <img src="{{ config('custom.symbolic_images_link') . $post->image }}" alt="" class="post__image">
                    </p>
                @endif
                <p class="post__meta">
                    Автор:
                    <a href="{{ route('public.search', 'query=' . $post->user->name) }}" class="post__author">{{ $post->user->name }}</a> |
                    Категория:
                    <a href="{{ route('public.search', 'query=' . $post->category->name) }}" class="post__category">{{ $post->category->name }}</a> |
                    Дата:
                    <span class="post__date">{{ $post->created_at->format('d.m.Y') }}</span>
                </p>
            </div>

            <div class="post__body">
                {!! $post->content !!}
            </div>

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
                @can('edit', $post)
                    <a href="{{ route('public.posts.edit', $post->slug) }}" class="button button_edit post__button">Редактировать</a>
                @endcan

                @can('delete', $post)
                    <form action="{{ route('public.posts.destroy', $post->id) }}" method="POST" class="post__delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button button_delete post__button">Удалить</button>
                    </form>
                @endcan
            </div>

            <div class="post__comments">
                <h2 class="post__comments-title">Комментарии:</h2>

                @foreach ($comments as $comment)
                    <div class="post__comment">
                        <div class="comment" data-id="{{ $comment->id }}">
                            <div class="comment__meta">
                                <span class="avatar">
                                    <img src="{{ config('custom.symbolic_images_link') . ($comment->user->avatar ?? $defaultAvatar) }}" alt="" class="avatar__image">
                                </span>
                                <div class="comment__info">
                                    <p class="comment__author">{{ $comment->user->name }}:</p>
                                    <p class="comment__date">{{ $comment->created_at }}</p>
                                </div>
                            </div>

                            <p class="comment__text">{{ $comment->content }}</p>
                            <p class="comment__actions">
                                <span class="comment__action comment__action_reply">Ответить</span>
                                @auth
                                    @if (auth()->user()->id == $comment->user->id && !$comment->hide_edit_buttons)
                                        <span class="comment__action-group">
                                            <span class="comment__action comment__action_edit">Изменить</span>
                                            <span class="comment__action comment__action_delete">Удалить</span>
                                        </span>
                                    @endif
                                @endauth
                            </p>
                        </div>

                        @if ($comment->replies->isNotEmpty())
                            <div class="comment__replies">
                                @foreach ($comment->replies as $reply)
                                    @include('public.posts.reply', ['reply' => $reply])
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </article>
    </section>

    <input type="hidden" value="{{ auth()->user()->id ?? 0 }}" id="auth_user">
    <input type="hidden" value="{{ $post->id }}" id="post_id">
    <input type="hidden" value="{{ route('public.comments.store') }}" id="base_url">
    <input type="hidden" value="{{ $defaultAvatar }}" id="default_avatar">

    <template id="reply-form-template">
        <form class="comment__form" action="" method="POST">
            <textarea class="comment__textarea" placeholder="Ваш ответ..." name="content"></textarea>
            <input type="submit" class="comment__submit-btn" value="Отправить">
        </form>
    </template>

    <template id="reply-form-edit-template">
        <form class="comment__form-edit" action="" method="POST">
            <textarea class="comment__textarea" name="content"></textarea>
            <input type="submit" class="comment__save-btn" value="Сохранить">
            <input type="button" class="comment__cancel-btn" value="Отменить">
        </form>
    </template>

    <template id="published-comment-template">
        <div class="">
            <div class="comment">
                <div class="comment__meta">
                    <span class="avatar">
                        <img src="" alt="" class="avatar__image">
                    </span>
                    <div class="comment__info">
                        <p class="comment__author"></p>
                        <p class="comment__date"></p>
                    </div>
                </div>

                <p class="comment__text"></p>

                <p class="comment__actions">
                    <span class="comment__action comment__action_reply">Ответить</span>
                    <span class="comment__action-group">
                        <span class="comment__action comment__action_edit" style="display: none;">Изменить</span>
                        <span class="comment__action comment__action_delete" style="display: none;">Удалить</span>
                    </span>
                </p>
            </div>
        </div>
    </template>

    <script type="module" src="{{ asset('/js/comments.js') }}"></script>
@endsection
