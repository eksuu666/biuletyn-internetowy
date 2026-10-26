@extends('panel::layouts.master', ['title' => 'Dodaj rolę'])

@section('content')
    <x-common-breadcrumbs>
        <li><a href="{{ route(config('app.panel_prefix', 'panel') . '.roles.index') }}">Role i uprawnienia</a></li>
        <li><a>Dodaj rolę</a></li>
    </x-common-breadcrumbs>

    <div class="row pe-0">
        <div class="col-12 pe-0">
            <div class="portlet box shadow min-height-500">
                <div class="portlet-heading">
                    <div class="portlet-title">
                        <h3 class="title">
                            <i class="icon-user-follow"></i>
                            Dodaj rolę
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
                    <form id="role-create-form" role="form" action="{{ route(config('app.panel_prefix', 'panel') . '.roles.store') }}" method="post">
                        @csrf
                        <x-common-error-messages/>

                        <fieldset class="row justify-content-center">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="name">Nazwa <small>(wymagane)</small> </label>
                                    <input id="name" class="form-control" name="name" type="text" required value="{{ old('name') }}">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="localName">Nazwa wyświetlana</label>
                                    <input id="localName" class="form-control" name="localName" type="text" value="{{ old('localName') }}">
                                </div>
                            </div>

                            <div class="col-12 d-flex row m-3">
                                <h2 class="mb-3 px-0">Uprawnienia roli</h2>
                                <div class="row mx-4 gap-3 gap-md-0">
                                    @foreach($groupedPermissions as $key => $permissions)
                                        <h3 class="mb-2 mt-4 px-0" style="margin-right: -1rem;">@lang('role::permissions.' . $key)</h3>
                                        @foreach($permissions as $permission)
                                            <div class="form-group col-lg-3 col-5 px-0">
                                                <label for="{{ $permission->id }}" class="cursor-pointer">
                                                    <input id="{{ $permission->id }}" class="form-control" name="permissions[]" type="checkbox" value="{{ $permission->name }}"
                                                           @if(old('permissions') && in_array($permission->name, old('permissions'))) checked @endif>
                                                    {{ $permission->local_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-4 mx-auto">
                                    <button class="btn btn-success btn-block">
                                        <i class="icon-check"></i>
                                        Dodaj rolę
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
        $("#role-create-form").validate();
    </script>
@endpush
