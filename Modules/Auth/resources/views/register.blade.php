@extends('auth::layouts.master', ['title' => 'Rejestracja'])

@section('content')
    <h1 id="account-heading">Zarejestruj się</h1>

    <form class="account-form" action="{{ route('register') }}" method="post">
        @csrf
        <label for="full_name">Nazwa użytkownika</label>
        <input id="full_name" name="full_name" value="{{ old('full_name') }}" required autocomplete="name">

        <label for="email">Adres e-mail</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">

        <label for="password">Hasło</label>
        <input id="password" name="password" type="password" minlength="8" required autocomplete="new-password">

        <label for="password_confirmation">Powtórz hasło</label>
        <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required autocomplete="new-password">

        <label class="account-check">
            <input type="checkbox" name="agree" required @checked(old('agree'))>
            Akceptuję regulamin serwisu
        </label>
        <button class="account-button" type="submit">Załóż konto</button>
    </form>

    <p class="account-switch">Masz już konto? <a href="{{ route('login') }}">Zaloguj się</a></p>
@endsection
