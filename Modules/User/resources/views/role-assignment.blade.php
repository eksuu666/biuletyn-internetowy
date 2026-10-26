@extends('panel::layouts.master', ['title' => 'Przypisz rolę użytkownikowi'])

@section('content')
    <x-common-breadcrumbs>
        <li><a href="{{ route(config('app.panel_prefix', 'panel') . '.users.index') }}">Użytkownicy</a></li>
        <li><a>Przypisz rolę użytkownikowi</a></li>
    </x-common-breadcrumbs>

    <div class="row pe-0">
        <div class="col-12 pe-0">
            <div class="portlet box shadow min-height-500">
                <div class="portlet-heading">
                    <div class="portlet-title">
                        <h3 class="title">
                            <i class="icon-user-follow"></i>
                            Przypisz rolę: {{ $user->full_name }}
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
                    <form id="main-form" role="form" action="{{ route(config('app.panel_prefix', 'panel') . '.users.role-assignment', $user->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <x-common-error-messages/>

                        <fieldset class="row justify-content-center">
                            <div class="col-lg-8 my-3">
                                <div class="roles-layout">
                                    @foreach($roles as $role)
                                        <div class="roles-item">
                                            <label for="{{ $role->name }}" class="cursor-pointer">
                                                <input id="{{ $role->name }}" class="form-control" name="roles[]" type="radio" value="{{ $role->id }}"
                                                      @checked(in_array($role->id, old('roles', $user->roles->pluck('id')->all())))>
                                                {{ $role->local_name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-4 mx-auto">
                                    <button class="btn btn-success btn-block">
                                        <i class="icon-check"></i>
                                        Przypisz rolę użytkownikowi
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

@push('styles')
    <style>
        .roles-layout {
            display: grid;
            grid-template-columns: repeat(4, auto);
            justify-content: space-between;
        }

        .roles-item {
            width: auto;
            word-break: break-all;
            margin-bottom: .5rem;
        }

        @media screen and (max-width: 575px) {
            .roles-layout {
                grid-template-columns: repeat(2, auto);
            }
        }
    </style>
@endpush
