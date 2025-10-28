@extends('layouts.admin')
@section('title', 'Комментарии')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Управление комментариями</h1>
    </div>

    <div class="content__body">
        <table class="table">
            <thead class="table__head">
            <tr class="table__row">
                <th class="table__cell" scope="col">ID</th>
                <th class="table__cell" scope="col">Автор</th>
                <th class="table__cell" scope="col">Содержание</th>
                <th class="table__cell" scope="col">Дата</th>
                <th class="table__cell" scope="col">Пост</th>
            </tr>
            </thead>
            <tbody class="table__body">
            @foreach ($comments as $comment)
                <tr class="table__row">
                    <td class="table__cell">
                        {{ $comment->id }}
                    </td>
                    <td class="table__cell">
                        <a href="{{ route('admin.users.show', $comment->user) }}" class="comment-author">{{ $comment->user->name }}</a>
                    </td>
                    <td class="table__cell">
                        {{ $comment->content }}
                        <div class="comment-actions">
                            @if (!$comment->hide_edit_buttons)
                                <a href="{{ route('admin.comments.edit', $comment) }}" class="link link_edit">
                                    Редактировать
                                </a>|

                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <input type="submit" value="Удалить" class="link-button link-button_delete">
                                </form>
                            @endif
                        </div>
                    </td>
                    <td class="table__cell">
                        <p class="comment-date">{{ $comment->created_at }}</p>
                    </td>
                    <td>
                        <a href="{{ route('admin.posts.show', $comment->post) }}" class="comment-post">{{ $comment->post->title }}</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $comments->links('vendor.pagination.custom') }}
    </div>

    <script type="module">
        import { dateFormatter } from "{{ asset('/js/date-formatter.js') }}";

        Array.from(document.querySelectorAll('.comment-date')).forEach((elem) => {
            dateFormatter(elem, elem.textContent);
        });
    </script>
@endsection

