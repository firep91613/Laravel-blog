<div class="comment__reply">
    <div class="comment"  data-id="{{ $reply->id }}">
        <div class="comment__meta">
            <span class="avatar">
                <img src="{{ config('custom.symbolic_images_link') . ($reply->user->avatar ?? $defaultAvatar) }}" alt="" class="avatar__image">
            </span>
            <div class="comment__info">
                <p class="comment__author">{{ $reply->user->name }}:</p>
                <p class="comment__date">{{ $reply->created_at }}</p>
            </div>
        </div>
        <p class="comment__text">{{ $reply->content }}</p>
        <p class="comment__actions">
            <span class="comment__action comment__action_reply">Ответить</span>
            @auth
                @if (auth()->user()->id == $reply->user->id && !$reply->hide_edit_buttons)
                    <span class="comment__action-group">
                        <span class="comment__action comment__action_edit">Изменить</span>
                        <span class="comment__action comment__action_delete">Удалить</span>
                    </span>
                @endif
            @endauth
        </p>
    </div>

    @if($reply->replies->isNotEmpty())
        <div class="comment__replies">
            @foreach ($reply->replies as $subreply)
                @include('public.posts.reply', ['reply' => $subreply])
            @endforeach
        </div>
    @endif
</div>



