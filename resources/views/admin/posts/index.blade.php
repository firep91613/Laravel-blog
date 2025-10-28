@extends('layouts.admin')
@section('title', 'Посты')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Управление постами</h1>
    </div>

    <div class="content__body">
        <table class="table">
            <thead class="table__head">
            <tr>
                <th scope="col" class="table__cell">ID</th>
                <th scope="col" class="table__cell">Название</th>
                <th scope="col" class="table__cell">Автор</th>
                <th scope="col" class="table__cell">Дата создания</th>
                <th scope="col" class="table__cell">Подробнее</th>
            </tr>
            </thead>
            <tbody class="table__body">
            @foreach ($posts as $post)
                <tr class="table__row">
                    <td class="table__cell">{{ $post->id }}</td>
                    <td class="table__cell">{{ $post->title }}</td>
                    <td class="table__cell">{{ $post->user->name }}</td>
                    <td class="table__cell table__cell_date">{{ $post->created_at->format('Y-m-d H:i:s') }}</td>
                    <td class="table__cell">
                        <a href="{{ route('admin.posts.show', $post) }}" class="table__link">Открыть</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.posts.create') }}" class="button button_add">Добавить новый пост</a>

    <div class="pagination">
        {{ $posts->links('vendor.pagination.custom') }}
    </div>

    <script type="module">
        import { dateFormatter } from "{{ asset('/js/date-formatter.js') }}"

        Array.from(document.querySelectorAll('.table__cell_date')).forEach((elem) => {
            dateFormatter(elem, elem.textContent);
        });
    </script>
@endsection
