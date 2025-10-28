@extends('layouts.admin')
@section('title', 'Группы')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Группа {{ $role->name }}</h1>
    </div>

    <div class="content__body">
        <table class="table">
            <thead class="table__head">
            <tr class="table__row">
                <th class="table__cell" scope="col">ID</th>
                <th class="table__cell" scope="col">Название</th>
                <th class="table__cell" scope="col">Действие</th>
            </tr>
            </thead>
            <tbody class="table__body">
            <tr class="table__row">
                <td class="table__cell">{{ $role->id }}</td>
                <td class="table__cell">{{ $role->name }}</td>
                <td class="table__cell">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="button button_edit button_edit-admin">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="table__form table__form_delete">
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
