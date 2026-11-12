<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <title>{{ $title ?? 'Panel redakcji' }} | sŁodziaki</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-brand-assets :site="$siteDetails ?? null"/>

    @include('panel::partials.styles')
    <link rel="stylesheet" href="{{ asset('brand/editorial.css') }}?v={{ filemtime(public_path('brand/editorial.css')) }}">

    <!-- BEGIN PAGE CSS -->
    <style>
        #page-content {
            min-height: 87vh;
        }

        .header-right {
            transition: .3s all;
        }

    </style>
    @stack('styles')
    <!-- END PAGE CSS -->
</head>
<body class="active-ripple theme-darkpurple fix-header sidebar-extra brand-panel">
<!-- BEGIN LOEADING -->
<div id="loader">
    <div class="spinner"></div>
</div><!-- /loader -->
<!-- END LOEADING -->

@include('panel::partials.header')

<!-- BEGIN WRAPPER -->
<div id="wrapper">

    @include('panel::partials.sidebar')

    <!-- BEGIN PAGE CONTENT -->
    <div id="page-content">
        <div id="inner-content">
            <div class="row">

                @yield('content')

            </div><!-- /.row -->
        </div><!-- /#inner-content -->
    </div><!-- /#page-content -->
    <!-- END PAGE CONTENT -->

</div><!-- /#wrapper -->
<!-- END WRAPPER -->

@include('panel::partials.footer')

@include('panel::partials.scripts')

<!-- BEGIN PAGE JAVASCRIPT -->
@stack('scripts')
<!-- END PAGE JAVASCRIPT -->

@include('sweetalert::alert')
</body>

</html>
