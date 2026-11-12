@extends('auth::layouts.master', ['title' => 'Odzyskiwanie hasła'])

@section('content')
    <h1 id="account-heading">Odzyskiwanie hasła</h1>
    <p class="account-intro">Podaj adres e-mail przypisany do Twojego konta.</p>
    <form class="account-form" method="post" action="{{ route('password.email') }}">
        @csrf
        <label for="email">Adres e-mail</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
        <button class="account-button" type="submit">Wyślij link do zmiany hasła</button>
    </form>
    <p class="account-switch"><a href="{{ route('login') }}">← Wróć do logowania</a></p>
@endsection
