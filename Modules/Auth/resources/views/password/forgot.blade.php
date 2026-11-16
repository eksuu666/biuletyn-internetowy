@extends('auth::layouts.master', ['title' => 'Odzyskiwanie hasła'])

@section('content')
    <h1 id="account-heading">Odzyskiwanie hasła</h1>
    @if(config('mail.default') === 'log')
        <p class="account-intro">Odzyskiwanie hasła przez e-mail jest obecnie niedostępne. Skontaktuj się z <a href="{{ route('contact-us.index') }}">redakcją</a>.</p>
    @else
        <p class="account-intro">Podaj adres e-mail przypisany do Twojego konta.</p>
        <form class="account-form" method="post" action="{{ route('password.email') }}">
            @csrf
            <label for="email">Adres e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
            <button class="account-button" type="submit">Wyślij link do zmiany hasła</button>
        </form>
    @endif
    <p class="account-switch"><a href="{{ route('login') }}">← Wróć do logowania</a></p>
@endsection
