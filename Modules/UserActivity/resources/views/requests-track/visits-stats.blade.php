@extends('panel::layouts.master', ['title' => 'Statystyki odsłon'])

@section('content')

    <x-common-breadcrumbs>
        <li><a>Statystyki odsłon</a></li>
    </x-common-breadcrumbs>

    <div class="row pe-0">
        <div class="col-12 pe-0">
            <div class="portlet box shadow min-height-500">
                <div class="portlet-heading">
                    <div class="portlet-title d-flex gap-3">
                        <h3 class="title m-0">
                            <i class="icon-eye"></i>
                            Statystyki odsłon
                        </h3>
                    </div><!-- /.portlet-title -->
                    <div class="buttons-box ltr">
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
                    <!-- Visit counts table -->
                    <div class="visit-counts">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Okres</th>
                                <th>Liczba odsłon</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Ostatnia godzina</td>
                                <td>{{ $visitCounts['hourly'] }}</td>
                            </tr>
                            <tr>
                                <td>Ostatnie 10 godzin</td>
                                <td>{{ $visitCounts['ten_hours'] }}</td>
                            </tr>
                            <tr>
                                <td>Ostatnia doba</td>
                                <td>{{ $visitCounts['daily'] }}</td>
                            </tr>
                            <tr>
                                <td>Ostatni tydzień</td>
                                <td>{{ $visitCounts['weekly'] }}</td>
                            </tr>
                            <tr>
                                <td>Ostatni miesiąc</td>
                                <td>{{ $visitCounts['monthly'] }}</td>
                            </tr>
                            <tr>
                                <td>Ostatni rok</td>
                                <td>{{ $visitCounts['yearly'] }}</td>
                            </tr>
                            <tr>
                                <td>Wszystkie odsłony</td>
                                <td>{{ $visitCounts['all'] }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div><!-- /.portlet-body -->
            </div><!-- /.portlet -->
        </div>
    </div>

@endsection
