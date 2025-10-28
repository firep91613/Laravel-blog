@extends('layouts.admin')
@section('title', 'Категории')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Категория {{ $category->name }}</h1>
    </div>

    <div class="content__body">
        <table class="table">
            <thead class="table__head">
            <tr class="table__row">
                <th class="table__cell" scope="col">ID</th>
                <th class="table__cell" scope="col">Название</th>
                <th class="table__cell" scope="col">Описание</th>
                <th class="table__cell" scope="col">Действие</th>
            </tr>
            </thead>
            <tbody class="table__body">
            <tr class="table__row">
                <td class="table__cell">{{ $category->id }}</td>
                <td class="table__cell">{{ $category->name }}</td>
                <td class="table__cell">{{ $category->description }}</td>
                <td class="table__cell">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="button button_edit button_edit-admin">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>

                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="table__form table__form_delete">
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
