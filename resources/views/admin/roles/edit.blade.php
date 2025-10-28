@extends('layouts.admin')
@section('title', 'Редактирование группы ' . $role->name)

@section('content')
    <div class="content__header">
        <h1 class="content__title">Редактирование группы {{ $role->name }}</h1>
    </div>

    <div class="content__body">
        <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="form">
            @csrf
            @method('PUT')

            <div class="form__group">
                <label for="name" class="form__label">Название группы</label>
                <input id="name" name="name" type="text" value="{{ old('name', $role->name) }}" class="form__input">

                @error('name')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <input type="submit" value="Сохранить" class="form__submit">
            </div>
        </form>
    </div>
@endsection
