@extends('layouts.admin')
@section('title', 'Настройки')

@section('content')
    <div class="content__header">
        <h1 class="content__title">Управление настройками</h1>
    </div>

    <div class="content__body">
        <table class="table">
            <thead class="table__head">
            <tr class="table__row">
                <th class="table__cell" scope="col">ID</th>
                <th class="table__cell" scope="col">Название</th>
                <th class="table__cell" scope="col">Значение</th>
                <th class="table__cell" scope="col">Действие</th>
            </tr>
            </thead>
            <tbody class="table__body">
            @foreach ($settings as $setting)
                <tr class="table__row">
                    <td class="table__cell table__cell_setting">{{ $setting->id }}</td>
                    <td class="table__cell table__cell_setting">{{ $setting->name }}</td>
                    <td class="table__cell table__cell_setting">
                        @if($setting->isImage($setting->value))
                            <img src="{{ config('custom.symbolic_images_link') . $setting->value }}" alt="" class="setting-image">
                        @else
                            {{ $setting->value }}
                        @endif
                    </td>
                    <td class="table__cell table__cell-setting">
                        <a class="table__link" href="{{ route('admin.settings.edit', $setting->slug) }}">Изменить</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
