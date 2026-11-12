@extends('panel::layouts.master', ['title' => 'Pamięć podręczna'])

@section('content')
    <x-common-breadcrumbs>
        <li><a>Ustawienia</a></li>
        <li><a>Pamięć podręczna</a></li>
    </x-common-breadcrumbs>

    <div class="row pe-0">
        <div class="col-12 pe-0">
            <div class="portlet box shadow min-height-500">
                <div class="portlet-heading">
                    <div class="portlet-title">
                        <h3 class="title">
                            <i class="icon-layers"></i>
                            Pamięć podręczna
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
                    @if(session()->has('success'))
                        <div class="alert alert-success m-t-10 m-b-20">
                            <i class="icon-check"></i>
                            {{ session()->get('success') }}
                        </div>
                    @endif

                    <div>
                        <div class="row justify-content-center">
                            @can(config('permissions_list.CACHE_CLEAR_ALL', false))
                                <div class="mt-3 col-12 row justify-content-center p-0">
                                    <div class="col-lg-6">
                                        <form action="{{ route(config('app.panel_prefix', 'panel') . '.settings.cache-management.clear-all') }}" method="post">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-block">
                                                <i class="icon-close"></i>
                                                Wyczyść całą pamięć podręczną
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endcan
                            @can(config('permissions_list.CACHE_CLEAR_APPLICATION', false))
                                <div class="mt-3 col-lg-6">
                                    <form action="{{ route(config('app.panel_prefix', 'panel') . '.settings.cache-management.clear-application') }}" method="post">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-block">
                                            <i class="icon-trash"></i>
                                            Wyczyść pamięć danych
                                        </button>
                                    </form>
                                </div>
                            @endcan
                            @can(config('permissions_list.CACHE_CLEAR_VIEW', false))
                                <div class="mt-3 col-lg-6">
                                    <form action="{{ route(config('app.panel_prefix', 'panel') . '.settings.cache-management.clear-view') }}" method="post">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-block">
                                            <i class="icon-eye"></i>
                                            Wyczyść pamięć widoków
                                        </button>
                                    </form>
                                </div>
                            @endcan
                            @can(config('permissions_list.CACHE_CLEAR_CONFIG', false))
                                <div class="mt-3 col-lg-6">
                                    <form action="{{ route(config('app.panel_prefix', 'panel') . '.settings.cache-management.clear-config') }}" method="post">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-block">
                                            <i class="icon-settings"></i>
                                            Wyczyść pamięć konfiguracji
                                        </button>
                                    </form>
                                </div>
                            @endcan
                            @can(config('permissions_list.CACHE_CLEAR_ROUTE', false))
                                <div class="mt-3 col-lg-6">
                                    <form action="{{ route(config('app.panel_prefix', 'panel') . '.settings.cache-management.clear-route') }}" method="post">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-block">
                                            <i class="icon-map"></i>
                                            Wyczyść pamięć tras
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div><!-- /.portlet-body -->
            </div><!-- /.portlet -->
        </div>
    </div>
@endsection

