@extends('auth::layouts.master', ['title' => 'Nowe hasło'])

@section('content')
    <h1 id="account-heading">Zapisz nowe hasło</h1>
    <form class="account-form" method="post" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <label for="password">Nowe hasło</label>
        <input id="password" name="password" type="password" minlength="8" required autocomplete="new-password">
        <label for="password_confirmation">Powtórz hasło</label>
        <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required autocomplete="new-password">
        <button class="account-button" type="submit">Zapisz hasło</button>
    </form>
    <p class="account-switch"><a href="{{ route('password.request') }}">Poproś o nowy link</a></p>
@endsection
