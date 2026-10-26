@extends('auth::layouts.master', ['title' => 'Logowanie'])

@section('content')
    <div class="logo-con m-t-10 m-b-10">
        <img src="{{ $siteDetails->mainLogoLink() }}" alt="{{ config('app.name') }}" class="dark-logo center-block img-responsive"
             width="256px">
        <img src="{{ $siteDetails->secondLogoLink() }}" alt="{{ config('app.name') }}" class="light-logo center-block img-responsive"
             width="256px">
    </div><!-- /.logo-con -->
    <h2 class="text-center m-b-20">Zaloguj się</h2>

    @if(session()->has('status') && session()->get('status') === 'success')
        <div class="alert alert-success m-t-10 m-b-20">
            <i class="icon-check"></i>
            {{ session()->get('message') }}
        </div>
    @endif

    <x-common-error-messages/>
    <hr>

    <form id="form" class="m-t-30 m-b-30" action="{{ route('login') }}" method="POST" role="form">
        @csrf

        <div class="form-group">
            <label for="email" class="sr-only">E-mail</label>
            <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="icon-envelope"></i>
                                    </span>
                <input id="email" class="form-control ltr text-left" type="email" name="email"
                       placeholder="info@site.com" required value="{{ old('email') }}">
            </div><!-- /.input-group -->
        </div><!-- /.form-group -->
        <div class="form-group">
            <label for="password" class="sr-only">Hasło</label>
            <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="icon-key"></i>
                                    </span>
                <input id="password" class="form-control ltr text-left" type="password" name="password" minlength="8"
                       required>
            </div><!-- /.input-group -->
        </div><!-- /.form-group -->
        <div class="form-group mt-2">
            <div class="input-group round">
                <div class="checkbox">
                    <label class="cursor-pointer" id="remember-me-label">
                        <input type="checkbox" id="remember-me" name="remember-me" @if(old('remember-me')) checked @endif>
                        Zapamiętaj mnie
                    </label>
                </div><!-- /.checkbox -->
            </div><!-- /.input-group-->
        </div><!-- /.form-group -->

        <p>
            <button class="btn btn-success btn-block" type="submit">
                <i class="icon-login font-lg"></i>
                Zaloguj się
            </button>
        </p>
    </form>
    <hr class="m-b-30">
    <a href="{{ route('password.request') }}" class="btn btn-default btn-block m-b-10">
        <i class="icon-refresh font-lg"></i>
        Odzyskiwanie hasła
    </a>
    <a href="{{ route('register') }}" class="btn btn-default btn-block">
        <i class="icon-user-follow font-lg"></i>
        Nie masz konta? Zarejestruj się!
    </a>
@endsection

@push('scripts')
    <!-- PAGE JAVASCRIPT -->
    <script src="{{ asset('admin/assets/js/pages/login.js') }}"></script>
    <!-- END PAGE JAVASCRIPT -->
@endpush
