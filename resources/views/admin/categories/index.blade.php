@extends('layouts.admin')
@section('title', 'Категории')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Управление категориями</h1>
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
            @foreach ($categories as $category)
                <tr class="table__row">
                    <td class="table__cell">{{ $category->id }}</td>
                    <td class="table__cell">{{ $category->name }}</td>
                    <td class="table__cell">
                        <a class="table__link" href="{{ route('admin.categories.show', $category) }}">Открыть</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.categories.create') }}" class="button button_add">Добавить новую категорию</a>

    <div class="pagination">
        {{ $categories->links('vendor.pagination.custom') }}
    </div>
@endsection

