@extends('layouts.admin')
@section('title', 'Группы')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Управление группами</h1>
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
            @foreach ($roles as $role)
                <tr class="table__row">
                    <td class="table__cell">{{ $role->id }}</td>
                    <td class="table__cell">{{ $role->name }}</td>
                    <td class="table__cell">
                        <a class="table__link" href="{{ route('admin.roles.show', $role) }}">Открыть</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.roles.create') }}" class="button button_add">Добавить новую группу</a>

    <div class="pagination">
        {{ $roles->links('vendor.pagination.custom') }}
    </div>
@endsection
