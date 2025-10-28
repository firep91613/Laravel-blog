@extends('layouts.public')
@section('title', 'Верификация email')

@section('content')
    <section class="verify-email">
        <p class="verify-email__message">
            Ссылка для подтверждения регистрации была отправлена на ваш E-mail.
        </p>
        <div class="verify-email__resend">
            <form action="{{ route('verification.send') }}" method="POST" class="verify-email__form">
                @csrf
                <input type="submit" value="Отправить письмо повторно" class="verify-email__submit button">
            </form>
        </div>
    </section>
@endsection
