@extends('auth::layouts.master', ['title' => 'Potwierdzenie e-maila'])

@section('content')
    <h1 id="account-heading">Potwierdź adres e-mail</h1>
    @if(config('mail.default') === 'log')
        <p class="account-intro">Potwierdzanie adresu e-mail jest obecnie niedostępne.</p>
    @else
        <p class="account-intro">Otwórz link potwierdzający wysłany na adres przypisany do konta.</p>
        <form class="account-form" action="{{ route('verification.send') }}" method="post">
            @csrf
            <button class="account-button" type="submit">Wyślij link ponownie</button>
        </form>
    @endif
    <form class="account-switch" action="{{ route('logout') }}" method="post">
        @csrf
        <button class="account-link" type="submit">Wyloguj się</button>
    </form>
@endsection
