@extends('auth::layouts.master', ['title' => 'Logowanie'])

@section('content')
    <h1 id="account-heading">Zaloguj się</h1>
    <p class="account-intro">Wejdź na swoje konto w sŁodziakach.</p>

    <form class="account-form" action="{{ route('login') }}" method="post">
        @csrf
        <label for="email">Adres e-mail</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username">

        <div class="account-field-label">
            <label for="password">Hasło</label>
            <a href="{{ route('password.request') }}">Nie pamiętam hasła</a>
        </div>
        <input id="password" name="password" type="password" required autocomplete="current-password">

        <label class="account-check">
            <input type="checkbox" name="remember-me" @checked(old('remember-me'))>
            Zapamiętaj mnie
        </label>
        <button class="account-button" type="submit">Zaloguj się</button>
    </form>

    <p class="account-switch">Nie masz konta? <a href="{{ route('register') }}">Załóż konto</a></p>
@endsection
