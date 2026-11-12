@extends('panel::layouts.master', ['title' => 'Dodaj stronę'])

@section('content')
    <x-common-breadcrumbs>
        <li><a href="{{ route(config('app.panel_prefix', 'panel') . '.pages.index') }}">Strony</a></li>
        <li><a>Dodaj stronę</a></li>
    </x-common-breadcrumbs>

    <div class="row pe-0">
        <div class="col-12 pe-0">
            <div class="portlet box shadow min-height-500">
                <div class="portlet-heading">
                    <div class="portlet-title">
                        <h3 class="title">
                            <i class="fas fa-laptop-file"></i>
                            Dodaj stronę
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
                    <form id="main-form" role="form" action="{{ route(config('app.panel_prefix', 'panel') . '.pages.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <x-common-error-messages/>

                        <fieldset class="row justify-content-center">
                            <div class="form-group col-lg-6">
                                <label for="title">Tytuł <small>(wymagane)</small></label>
                                <input id="title" class="form-control" name="title" type="text" required value="{{ old('title') }}">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="slug">slug <small>(wymagane)</small> </label>
                                <input id="slug" class="form-control" name="slug" type="text" required value="{{ old('slug') }}">
                            </div>
                            <div class="form-group col-12">
                                <label for="tinymce-editor">Treść <small>(wymagane)</small></label>
                                <textarea id="tinymce-editor" name="content" required>{{ old('content') }}</textarea>
                            </div>
                            <div class="form-group relative col-lg-6">
                                <label>Obraz główny <small>(wymagane)</small></label>
                                <div class="input-group round">
                                    <input type="text" class="form-control file-input" placeholder="Wybierz plik">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-success">
                                            <i class="icon-picture"></i>
                                            Prześlij obraz</button>
                                    </span>
                                </div><!-- /.input-group -->
                                <input type="file" class="form-control" name="image" required>
                                <div class="help-block"></div>
                            </div>
                            <div class="col-12 text-center form-group">
                                <div>
                                    <label for="status">Status <small>(wymagane)</small></label>
                                    <input id="status" class="form-control" name="status" type="checkbox" @if(old('status')) checked @endif>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-4 mx-auto">
                                    <button class="btn btn-success btn-block">
                                        <i class="icon-check"></i>
                                        Dodaj stronę
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
    <script src="{{ asset('admin/assets/plugins/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/select2/dist/js/i18n/fa.js') }}"></script>
    <script src="{{ asset('admin/assets/js/pages/select2.js') }}"></script>

    @include('common::partials.tinymce-scripts')

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
            },
        });
        $("#main-form").validate();
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/tinymce.css') }}">
@endpush
