@extends('layouts.admin')
@section('title', 'Теги')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Тег {{ $tag->name }}</h1>
    </div>

    <div class="content__body">
        <table class="table">
            <thead class="table__head">
            <tr class="table__row">
                <th class="table__cell" scope="col">ID</th>
                <th class="table__cell" scope="col">Название</th>
                <th class="table__cell" scope="col">Слаг</th>
                <th class="table__cell" scope="col">Действие</th>
            </tr>
            </thead>
            <tbody class="table__body">
            <tr class="table__row">
                <td class="table__cell">{{ $tag->id }}</td>
                <td class="table__cell">{{ $tag->name }}</td>
                <td class="table__cell">{{ $tag->slug }}</td>
                <td class="table__cell">
                    <a href="{{ route('admin.tags.edit', $tag) }}" class="button button_edit button_edit-admin">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="table__form table__form_delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button button_delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
