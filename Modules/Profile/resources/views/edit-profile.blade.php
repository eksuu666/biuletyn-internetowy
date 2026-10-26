@extends('panel::layouts.master', ['title' => 'Edytuj profil autora'])

@section('content')
    <x-common-breadcrumbs>
        <li><a>Profil</a></li>
        <li><a>Edytuj profil autora</a></li>
    </x-common-breadcrumbs>

    <div class="row pe-0">
        <div class="col-12 pe-0">
            <div class="portlet box shadow min-height-500">
                <div class="portlet-heading">
                    <div class="portlet-title">
                        <h3 class="title">
                            <i class="far fa-pen-to-square"></i>
                            Edytuj profil autora
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
                    <form id="main-form" role="form" action="{{ route(config('app.panel_prefix', 'panel') . '.profile.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <x-common-error-messages/>

                        <fieldset class="row justify-content-center">
                            <div class="form-group col-lg-6">
                                <label for="full_name">Imię i nazwisko lub nazwa autora <small>(wymagane)</small></label>
                                <input id="full_name" class="form-control" name="full_name" type="text" required value="{{ old('full_name', $user->full_name) }}">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="username">Nazwa w adresie profilu <small>(wymagane)</small></label>
                                <input id="username" class="form-control" name="username" type="text" required value="{{ old('username', $user->username) }}">
                            </div>
                            <div class="form-group col-12 row justify-content-center">
                                <div class="col-md-6">
                                    <label for="bio">Krótki opis autora (do 600 znaków)</label>
                                    <textarea maxlength="600" class="form-control" name="bio" id="bio">{{ old('bio', $user->bio) }}</textarea>
                                </div>
                            </div>
                            <div class="col-12 d-flex flex-column align-items-center">
                                <div class="form-group relative col-lg-6">
                                    <label>Zdjęcie autora (opcjonalne) <small>(wymagane)</small></label>
                                    <div class="input-group round">
                                        <input type="text" class="form-control file-input" placeholder="Wybierz zdjęcie">
                                        <span class="input-group-btn">
                                        <button type="button" class="btn btn-success">
                                            <i class="icon-picture"></i>
                                            Wgraj zdjęcie</button>
                                    </span>
                                    </div>
                                    <input type="file" class="form-control" name="picture">
                                    <div class="help-block"></div>
                                </div>
                                <div class="form-group col-12 text-center">
                                    <img class="mb-2" src="{{ $user->avatarUrl() }}" alt="{{ $user->full_name }}" style="max-width: 300px; max-height:
                                    300px">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-4 mx-auto">
                                    <button class="btn btn-success btn-block">
                                        <i class="icon-check"></i>
                                        Edytuj profil autora
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
        $("#main-form").validate();
    </script>
@endpush
