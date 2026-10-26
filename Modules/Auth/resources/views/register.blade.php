@extends('auth::layouts.master', ['title' => 'Zarejestruj się'])

@section('content')
    <p class="text-center m-t-30 m-b-40">
        <i class="icon-user-follow border img-circle font-xxxlg p-20"></i>
    </p>
    <h2 class="text-center m-b-20">Zarejestruj się </h2>

    <x-common-error-messages/>

    <hr>

    <form id="advanced-form" method="post" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label class="sr-only control-label" for="username">Nazwa</label>
            <div class="input-group round">
                <span class="input-group-addon">
                    <i class="icon-user"></i>
                </span>
                <input type="text" class="form-control round" id="full_name" name="full_name" value="{{ old('full_name') }}">
            </div><!-- /.input-group-->
        </div><!-- /.form-group -->
        <div class="form-group">
            <label class="sr-only control-label" for="email">E-mail</label>
            <div class="input-group round">
                <span class="input-group-addon">
                    <i class="icon-envelope"></i>
                </span>
                <input type="text" class="form-control round ltr text-left" id="email" name="email"
                       placeholder="info@site.com" value="{{ old('email') }}">
            </div><!-- /.input-group-->
        </div><!-- /.form-group -->
        <div class="form-group">
            <label class="sr-only control-label" for="password">Hasło</label>
            <div class="input-group round">
                <span class="input-group-addon">
                    <i class="icon-key"></i>
                </span>
                <input type="password" class="form-control round rtl text-left" id="password" name="password"
                       minlength="8">
            </div><!-- /.input-group-->
        </div><!-- /.form-group -->
        <div class="form-group">
            <label class="sr-only control-label" for="confirm_password">Powtórz hasło</label>
            <div class="input-group round">
                <span class="input-group-addon">
                    <i class="icon-key"></i>
                </span>
                <input type="password" class="form-control round rtl text-left" id="password_confirmation"
                       name="password_confirmation" minlength="8">
            </div><!-- /.input-group-->
        </div><!-- /.form-group -->
        <div class="form-group mt-2">
            <div class="input-group round">
                <div class="checkbox">
                    <label class="cursor-pointer" id="agree-label">
                        <input type="checkbox" id="agree" name="agree" @if(old('agree')) checked @endif>
                        Akceptuję regulamin serwisu
                    </label>
                </div><!-- /.checkbox -->
            </div><!-- /.input-group-->
        </div><!-- /.form-group -->
        <div class="form-group">
            <button type="submit" class="btn btn-success btn-block">
                <i class="icon-check"></i>
                Zarejestruj się
            </button>
        </div><!-- /.form-group -->
    </form>

    <hr class="m-b-30">
    <a href="{{ route('password.request') }}" class="btn btn-default btn-block m-b-10">
        <i class="icon-refresh font-lg"></i>
        Odzyskiwanie hasła
    </a>
    <a href="{{ route('login') }}" class="btn btn-default btn-block">
        <i class="icon-user-following font-lg"></i>
        Masz już konto? Zaloguj się!
    </a>
@endsection

@push('scripts')
    <!-- PAGE JAVASCRIPT -->
    <script src="{{ asset('admin/assets/js/pages/register.js') }}"></script>
    <!-- END PAGE JAVASCRIPT -->
@endpush
