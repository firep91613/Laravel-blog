@extends('layouts.admin')
@section('title', 'Пользователи')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Управление пользователями</h1>
    </div>

    <div class="content__body">
        <div class="filter">
            <form action="?" method="GET" class="filter__form">
                <div class="filter__group">
                    <label for="id" class="filter_label">ID:</label>
                    <input type="text" id="id" name="id" value="{{ request('id') }}" class="filter__input">
                </div>

                <div class="filter__group">
                    <label for="name" class="filter__label">Имя:</label>
                    <input type="text" id="name" name="name" value="{{ request('name') }}" class="filter__input">
                </div>

                <div class="filter__group">
                    <label for="email" class="filter__label">Email:</label>
                    <input type="email" id="email" name="email" value="{{ request('email') }}" class="filter__input">
                </div>

                <div class="filter__group">
                    <input type="submit" id="email" value="Фильтровать" class="filter__submit">
                </div>
            </form>
        </div>

        <table class="table">
            <thead class="table__head">
            <tr class="table__row">
                <th class="table__cell" scope="col">ID</th>
                <th class="table__cell" scope="col">Имя</th>
                <th class="table__cell" scope="col">E-mail</th>
                <th class="table__cell" scope="col">Дата регистрации</th>
                <th class="table__cell" scope="col">Роль</th>
                <th class="table__cell" scope="col">Статус</th>
                <th class="table__cell" scope="col">Действие</th>
            </tr>
            </thead>
            <tbody class="table__body">
            @foreach ($users as $user)
                <tr class="users__table-row">
                    <td class="table__cell">{{ $user->id }}</td>
                    <td class="table__cell">{{ $user->name }}</td>
                    <td class="table__cell">{{ $user->email }}</td>
                    <td class="table__cell table__cell_date">{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                    <td class="table__cell">{{ $user->role->name }}</td>
                    <td class="table__cell">{{ $user->isVerified() }}</td>
                    <td class="table__cell">
                        <a href="{{ route('admin.users.show', $user) }}" class="table__link">Открыть</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.users.create') }}" class="button button_add">Добавить нового пользователя</a>

    <div class="pagination">
        {{ $users->links('vendor.pagination.custom') }}
    </div>

    <script type="module">
        import { dateFormatter } from "{{ asset('/js/date-formatter.js') }}"

        Array.from(document.querySelectorAll('.table__cell_date')).forEach((elem) => {
            dateFormatter(elem, elem.textContent);
        });
    </script>
@endsection
