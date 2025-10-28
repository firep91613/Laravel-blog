@extends('layouts.admin')
@section('title', 'Настройки')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Изменение "{{ $setting->name }}"</h1>
    </div>

    <div class="content__body">
        <form action="{{ route('admin.settings.update', $setting->slug) }}" method="POST" enctype="multipart/form-data" class="form">
            @csrf
            @method('PUT')

            <div class="form__group">
                @include('admin.settings.parts.' . $setting->slug)

                @error("{{ $setting->slug }}")
                    <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <input type="submit" value="Сохранить" class="form__submit">
            </div>
        </form>
    </div>
@endsection

