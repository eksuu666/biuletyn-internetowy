@extends('front::layouts.master')
@php($accountSection = $accountSection ?? 'profile')
@php(\Artesaos\SEOTools\Facades\SEOTools::setTitle('Moje konto'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('home/css/account.css') }}?v={{ filemtime(public_path('home/css/account.css')) }}">
@endpush

@section('content')
    <div class="portal-wrap member-account">
        <h1>Moje konto</h1>
        <nav class="member-tabs" aria-label="Ustawienia konta">
            <a href="{{ route(config('app.panel_prefix').'.profile.edit') }}" @if($accountSection === 'profile') aria-current="page" @endif>Profil</a>
            <a href="{{ route(config('app.panel_prefix').'.profile.password.change') }}" @if($accountSection === 'password') aria-current="page" @endif>Hasło</a>
            @if(config('mail.default') !== 'log')<a href="{{ route(config('app.panel_prefix').'.profile.email.change') }}" @if($accountSection === 'email') aria-current="page" @endif>E-mail</a>@endif
            <form method="post" action="{{ route('logout') }}">@csrf<button type="submit">Wyloguj się</button></form>
        </nav>
        @if(session('success'))<p class="member-status" role="status">{{ session('success') }}</p>@endif
        @if(session('error'))<p class="member-error" role="alert">{{ session('error') }}</p>@endif
        <x-common-error-messages/>

        @if($accountSection === 'profile')
            <form class="member-form" method="post" action="{{ route(config('app.panel_prefix').'.profile.update') }}" enctype="multipart/form-data">
                @csrf @method('PATCH')
                <div class="member-avatar">
                    <img src="{{ $user->avatarUrl() }}" width="96" height="96" alt="Twoje zdjęcie profilowe" data-avatar-preview>
                    <div><label for="member-picture">Zdjęcie profilowe</label><input id="member-picture" type="file" name="picture" accept="image/*" data-avatar-input><small>Do 5 MB.</small><p class="member-error" data-avatar-error role="status"></p></div>
                </div>
                <label for="member-name">Imię i nazwisko lub nazwa</label>
                <input id="member-name" name="full_name" autocomplete="name" required minlength="2" maxlength="255" value="{{ old('full_name', $user->full_name) }}">
                <input type="hidden" name="username" value="{{ old('username', $user->username) }}">
                <label for="member-bio">O mnie</label>
                <textarea id="member-bio" name="bio" rows="3" maxlength="600">{{ old('bio', $user->bio) }}</textarea>
                <label for="member-email">Adres e-mail</label>
                <input id="member-email" type="email" value="{{ $user->email }}" readonly>
                <button class="portal-button" type="submit">Zapisz zmiany</button>
            </form>
        @elseif($accountSection === 'password')
            <form class="member-form" method="post" action="{{ route(config('app.panel_prefix').'.profile.password.update') }}">
                @csrf @method('PATCH')
                <label for="current-password">Obecne hasło</label><input id="current-password" type="password" name="password" required autocomplete="current-password">
                <label for="new-password">Nowe hasło</label><input id="new-password" type="password" name="new_password" minlength="8" required autocomplete="new-password">
                <label for="confirm-password">Powtórz nowe hasło</label><input id="confirm-password" type="password" name="new_password_confirmation" minlength="8" required autocomplete="new-password">
                <button class="portal-button" type="submit">Zmień hasło</button>
            </form>
        @elseif(config('mail.default') !== 'log')
            <form class="member-form" method="post" action="{{ route(config('app.panel_prefix').'.profile.email.update') }}">
                @csrf @method('PATCH')
                <label for="new-email">Nowy adres e-mail</label><input id="new-email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                <button class="portal-button" type="submit">Zmień e-mail</button>
            </form>
        @else
            <p class="member-form">Zmiana adresu e-mail jest obecnie niedostępna.</p>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('home/js/avatar-preview.js') }}?v={{ filemtime(public_path('home/js/avatar-preview.js')) }}" defer></script>
@endpush
