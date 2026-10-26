@extends('panel::layouts.master', ['title' => 'Kokpit redakcyjny'])

@section('content')
    <x-common-breadcrumbs :noprefix="true">
        <li><a>Kokpit</a></li>
    </x-common-breadcrumbs>

    {{-- Header Stats --}}
    @include('panel::content-sections/header-stats')

    <div class="row m-0 p-0">
        {{-- Site Visitors --}}
        @include('panel::content-sections.site-visitors')

        {{-- Articles Visits --}}
        @include('panel::content-sections.articles-visits')

        {{-- Articles --}}
        @include('panel::content-sections/articles')

        {{-- Categories --}}
        @include('panel::content-sections/categories')

        {{-- Tags --}}
        @include('panel::content-sections/tags')

        {{-- Images --}}
        @include('panel::content-sections/images')

        {{-- Comments --}}
        @include('panel::content-sections/comments')
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('admin/assets/plugins/jquery-incremental-counter/jquery.incremental-counter.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/morris.js/morris.min.js') }}"></script>
    <script>
        $(".counter-down").incrementalCounter({digits: 'auto'});
    </script>
    <script>
        Morris.Donut({
            element: 'site-visits-yearly',
            data: [
                {value: {{ $visitsCount['yearly'] }}, label: 'Rok', formatted: '{{ $visitsCount['yearly'] }} odsłon'},
                {value: {{ $visitsCount['monthly'] }}, label: 'Miesiąc', formatted: '{{ $visitsCount['monthly'] }} odsłon'},
                {value: {{ $visitsCount['weekly'] }}, label: 'Tydzień', formatted: '{{ $visitsCount['weekly'] }} odsłon'},
            ],
            colors: [
                '#1e4572',
                '#597bbd',
                '#6da1f1',
            ],
            formatter: function (x, data) {
                return data.formatted;
            },
            resize: true
        });

        Morris.Donut({
            element: 'site-visits-daily',
            data: [
                {value: {{ $visitsCount['daily'] }}, label: 'Dziś', formatted: '{{ $visitsCount['daily'] }} odsłon'},
                {value: {{ $visitsCount['ten_hours'] }}, label: '10 godz.', formatted: '{{ $visitsCount['ten_hours'] }} odsłon'},
                {value: {{ $visitsCount['hourly'] }}, label: '1 godz.', formatted: '{{ $visitsCount['hourly'] }} odsłon'},
            ],
            colors: [
                '#ffc107',
                '#e36100',
                '#d50000',
            ],
            formatter: function (x, data) {
                return data.formatted;
            },
            resize: true
        });
    </script>
    <script>
        Morris.Donut({
            element: 'articles-visits-yearly',
            data: [
                {value: {{ $articlesVisitsCount['year'] }}, label: 'Rok', formatted: '{{ $articlesVisitsCount['year'] }} odsłon'},
                {value: {{ $articlesVisitsCount['month'] }}, label: 'Miesiąc', formatted: '{{ $articlesVisitsCount['month'] }} odsłon'},
                {value: {{ $articlesVisitsCount['week'] }}, label: 'Tydzień', formatted: '{{ $articlesVisitsCount['week'] }} odsłon'},
            ],
            colors: [
                '#1e4572',
                '#597bbd',
                '#6da1f1',
            ],
            formatter: function (x, data) {
                return data.formatted;
            },
            resize: true
        });

        Morris.Donut({
            element: 'articles-visits-daily',
            data: [
                {value: {{ $articlesVisitsCount['day'] }}, label: 'Dziś', formatted: '{{ $articlesVisitsCount['day'] }} odsłon'},
                {value: {{ $articlesVisitsCount['10hours'] }}, label: '10 godz.', formatted: '{{ $articlesVisitsCount['10hours'] }} odsłon'},
                {value: {{ $articlesVisitsCount['hour'] }}, label: '1 godz.', formatted: '{{ $articlesVisitsCount['hour'] }} odsłon'},
            ],
            colors: [
                '#ffc107',
                '#e36100',
                '#d50000',
            ],
            formatter: function (x, data) {
                return data.formatted;
            },
            resize: true
        });
    </script>
@endpush
