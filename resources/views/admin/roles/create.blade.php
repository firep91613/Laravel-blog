@extends('layouts.admin')
@section('title', 'Добавление новой группы')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Создать новую группу</h1>
    </div>

    <div class="content__body">
        <form action="{{ route('admin.roles.store') }}" method="POST" class="form">
            @csrf

            <div class="form__group">
                <label for="name" class="form__label">Название группы</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="form__input">

                @error('name')
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <input type="submit" value="Создать" class="form__submit">
            </div>
        </form>
    </div>
@endsection
