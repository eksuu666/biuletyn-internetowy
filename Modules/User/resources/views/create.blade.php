@extends('panel::layouts.master', ['title' => 'Dodaj użytkownika'])

@section('content')
    <x-common-breadcrumbs>
        <li><a href="{{ route(config('app.panel_prefix', 'panel') . '.users.index') }}">Użytkownicy</a></li>
        <li><a>Dodaj użytkownika</a></li>
    </x-common-breadcrumbs>

    <div class="row pe-0">
        <div class="col-12 pe-0">
            <div class="portlet box shadow min-height-500">
                <div class="portlet-heading">
                    <div class="portlet-title">
                        <h3 class="title">
                            <i class="icon-user-follow"></i>
                            Dodaj użytkownika
                        </h3>
                    </div><!-- /.portlet-title -->
                    <div class="buttons-box">
                        <a class="btn btn-sm btn-default btn-round btn-fullscreen" rel="tooltip"
                           aria-label="Pełny ekran" data-bs-original-title="Pełny ekran">
                            <i class="icon-size-fullscreen d-flex justify-content-center align-items-center"></i>
                            <div class="paper-ripple">
                                <div class="paper-ripple__background"></div>
                                <div class="paper-ripple__waves"></div>
                            </div>
                        </a>
                    </div><!-- /.buttons-box -->
                </div><!-- /.portlet-heading -->
                <div class="portlet-body">
                    <form id="main-form" role="form" action="{{ route(config('app.panel_prefix', 'panel') . '.users.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <x-common-error-messages/>

                        <fieldset class="row justify-content-center">
                            <div class="form-group col-lg-6">
                                <label for="full_name">Nazwa <small>(wymagane)</small></label>
                                <input id="full_name" class="form-control" name="full_name" type="text" required value="{{ old('full_name') }}">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="username">Nazwa użytkownika <small>(wymagane)</small></label>
                                <input id="username" class="form-control" name="username" type="text" required value="{{ old('username') }}">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="email">E-mail <small>(wymagane)</small> </label>
                                <input id="email" class="form-control" name="email" type="email" required value="{{ old('email') }}">
                            </div>
                            <div class="form-group relative col-lg-6">
                                <label>Zdjęcie użytkownika (opcjonalne) <small>(wymagane)</small></label>
                                <div class="input-group round">
                                    <input type="text" class="form-control file-input" placeholder="Wybierz plik">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-success">
                                            <i class="icon-picture"></i>
                                            Wgraj zdjęcie</button>
                                    </span>
                                </div><!-- /.input-group -->
                                <input type="file" class="form-control" name="picture" accept="image/*">
                                <div class="help-block"></div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="password">Hasło <small>(wymagane, minimum 8 znaków)</small></label>
                                <input id="password" class="form-control" name="password" minlength="8" type="password" required value="{{ old('password') }}">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="password_confirmation">Powtórz hasło <small>(wymagane, minimum 8 znaków)</small></label>
                                <input id="password_confirmation" class="form-control" name="password_confirmation" minlength="8" type="password" required
                                       value="{{ old('password_confirmation') }}">
                            </div>
                            <div class="form-group col-12 row justify-content-center">
                                <div class="col-md-6">
                                    <label for="bio">Krótki opis autora</label>
                                    <textarea class="form-control" name="bio" id="bio">{{ old('bio') }}</textarea>
                                </div>
                            </div>
                            <div class="form-group col-12 row justify-content-center">
                                <div class="row justify-content-center col-6 row">
                                    <div class="text-center col-6">
                                        <input id="email_verification" class="form-control" name="email_verification" type="checkbox"
                                               @if(old('email_verification')) checked @endif>
                                        <label for="email_verification">Potwierdzenie e-maila</label>
                                    </div>
                                    <div class="text-center col-6">
                                        <input id="status" class="form-control" name="status" type="checkbox"
                                               @if(old('status')) checked @endif>
                                        <label for="status">Status</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-4 mx-auto">
                                    <button class="btn btn-success btn-block">
                                        <i class="icon-check"></i>
                                        Dodaj użytkownika
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div><!-- /.portlet-body -->
            </div><!-- /.portlet -->
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $.validator.setDefaults({
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error').removeClass("has-success");
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass("has-success");
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
        $("#main-form").validate({
            rules: {
                password_confirmation: {
                    equalTo: "#password"
                }
            },
            messages: {
                password_confirmation: {
                    equalTo: "Hasła muszą być takie same"
                }
            }
        });
    </script>
@endpush
