@extends('panel::layouts.master', ['title' => 'Dodaj wiadomość'])

@section('content')
    <x-common-breadcrumbs>
        <li><a href="{{ route(config('app.panel_prefix', 'panel') . '.articles.index') }}">Wiadomości</a></li>
        <li><a>Dodaj wiadomość</a></li>
    </x-common-breadcrumbs>

    <div class="row pe-0">
        <div class="col-12 pe-0">
            <div class="portlet box shadow min-height-500">
                <div class="portlet-heading">
                    <div class="portlet-title">
                        <h3 class="title">
                            <i class="icon-user-follow"></i>
                            Dodaj wiadomość
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
                    <form id="main-form" role="form" action="{{ route(config('app.panel_prefix', 'panel') . '.articles.store') }}" method="post" enctype="multipart/form-data">
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
                            <div class="form-group col-lg-6">
                                <label for="published_at">Data publikacji <small>(wymagane)</small></label>
                                <input id="published_at" name="published_at" type="datetime-local" class="form-control" required value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                                <small>Publikacja nastąpi o wskazanej godzinie (Europe/Warsaw), jeśli zaznaczysz „Opublikuj / zaplanuj”. Bez zaznaczenia materiał pozostaje szkicem.</small>
                            </div>
                            <div class="form-group relative col-lg-6">
                                <label>Zdjęcie główne <small>(wymagane)</small></label>
                                <div class="input-group round">
                                    <input type="text" class="form-control file-input" placeholder="Wybierz plik">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-success">
                                            <i class="icon-picture"></i>
                                            Wgraj zdjęcie</button>
                                    </span>
                                </div><!-- /.input-group -->
                                <input type="file" class="form-control" name="image" required>
                                <div class="help-block"></div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="category_id">Kategoria <small>(wymagane)</small></label>
                                <select id="category_id" class="form-control select2" name="category_id">
                                    <option value="">Wybierz kategorię</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @if((int) old('category_id') === $category->id) selected @endif>{{ $category->name
                                        }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="type">Rodzaj publikacji <small>(wymagane)</small></label>
                                <select id="type" class="form-control select2" name="type">
                                    <option value="">Wybierz rodzaj publikacji</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" @if(old('type') === $type) selected @endif>{{ __('article::types.' . $type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 d-flex justify-content-center">
                                <div class="col-12 col-md-6">
                                    <label for="tag_ids">Tagi</label>
                                    <select id="tag_ids" class="form-control select2" name="tag_ids[]" multiple>
                                        @foreach($tags as $tag)
                                            <option value="{{ $tag->id }}" @if(in_array($tag->id, old('tag_ids', []))) selected @endif>{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-12">
                                <label for="tinymce-editor">Treść wiadomości <small>(wymagane)</small></label>
                                <textarea id="tinymce-editor" name="body" required>{{ old('body') }}</textarea>
                            </div>
                            <div class="col-12 col-md-6 row form-group justify-content-center">
                                @can(config('permissions_list.ARTICLE_EDITOR_CHOICE', false))
                                    <div class="text-center col-4">
                                        <input id="editor_choice" class="form-control" name="editor_choice" type="checkbox" @if(old('editor_choice')) checked @endif>
                                        <label for="editor_choice">Wybór redakcji</label>
                                    </div>
                                @endcanany
                                @can(config('permissions_list.ARTICLE_HOTNESS', false))
                                    <div class="text-center col-4">
                                        <input id="hotness" class="form-control" name="hotness" type="checkbox" @if(old('hotness')) checked @endif>
                                        <label for="hotness">Pilne</label>
                                    </div>
                                @endcanany
                                <div class="text-center col-4">
                                    <input id="status" class="form-control" name="status" type="checkbox" @if(old('status')) checked @endif>
                                    <label for="status">Opublikuj / zaplanuj</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-4 mx-auto">
                                    <button class="btn btn-success btn-block">
                                        <i class="icon-check"></i>
                                        Dodaj wiadomość
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
