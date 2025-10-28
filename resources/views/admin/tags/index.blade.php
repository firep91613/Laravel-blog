@extends('layouts.admin')
@section('title', 'Теги')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Управление тегами</h1>
    </div>

    <div class="content__body">
        <table class="table">
            <thead class="table__head">
            <tr class="table__row">
                <th class="table__cell" scope="col">ID</th>
                <th class="table__cell" scope="col">Название</th>
                <th class="table__cell" scope="col">Подробнее</th>
            </tr>
            </thead>
            <tbody class="table__body">
            @foreach ($tags as $tag)
                <tr class="table__row">
                    <td class="table__cell">{{ $tag->id }}</td>
                    <td class="table__cell">{{ $tag->name }}</td>
                    <td class="table__cell">
                        <a class="table__link" href="{{ route('admin.tags.show', $tag) }}">Открыть</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.tags.create') }}" class="button button_add">Добавить новый тег</a>

    <div class="pagination">
        {{ $tags->links('vendor.pagination.custom') }}
    </div>
@endsection
