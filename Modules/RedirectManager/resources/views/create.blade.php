@extends('panel::layouts.master', ['title' => 'Dodaj przekierowanie'])

@section('content')
    <x-common-breadcrumbs>
        <li><a href="{{ route(config('app.panel_prefix', 'panel') . '.redirects.index') }}">Przekierowania</a></li>
        <li><a>Dodaj przekierowanie</a></li>
    </x-common-breadcrumbs>

    <div class="row pe-0">
        <div class="col-12 pe-0">
            <div class="portlet box shadow min-height-500">
                <div class="portlet-heading">
                    <div class="portlet-title">
                        <h3 class="title">
                            <i class="fas fa-link"></i>
                            Dodaj przekierowanie
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
                    <form id="main-form" role="form" action="{{ route(config('app.panel_prefix', 'panel') . '.redirects.store') }}" method="post">
                        @csrf
                        <x-common-error-messages/>

                        <fieldset class="row justify-content-center">
                            <div class="form-group col-lg-6">
                                <label for="source_url">URL Źródło <small>(wymagane)</small></label>
                                <input id="source_url" class="form-control" name="source_url" type="text" required value="{{ old('source_url') }}">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="destination_url">URL Cel <small>(wymagane)</small> </label>
                                <input id="destination_url" class="form-control" name="destination_url" type="text" required value="{{ old('destination_url') }}">
                            </div>
                            <div class="col-12 row form-group justify-content-center">
                                <div class="col-md-6 row justify-content-center">
                                    <div class="col-6">
                                        <label for="status_code">Kod HTTP</label>
                                        <input id="status_code" class="form-control" name="status_code" type="text" value="{{ old('status_code', '301') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 row form-group justify-content-center">
                                <div class="text-center col-6">
                                    <input id="status" class="form-control" name="status" type="checkbox" @if(old('status')) checked @endif>
                                    <label for="status">Status</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-4 mx-auto">
                                    <button class="btn btn-success btn-block">
                                        <i class="icon-check"></i>
                                        Dodaj przekierowanie
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
