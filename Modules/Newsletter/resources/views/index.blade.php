@extends('panel::layouts.master', ['title' => 'Subskrybenci newslettera'])

@section('content')

    <x-common-breadcrumbs>
        <li><a>Subskrybenci newslettera</a></li>
    </x-common-breadcrumbs>

    <div class="row pe-0">
        <div class="col-12 pe-0">
            <div class="portlet box shadow min-height-500">
                <div class="portlet-heading">
                    <div class="portlet-title d-flex gap-3">
                        <h3 class="title m-0">
                            <i class="icon-paper-plane"></i>
                            Subskrybenci newslettera
                        </h3>
                        <form class="d-inline-block search-form">
                            <div class="input-group">
                                <button class="btn btn-secondary d-flex align-items-center" type="submit">
                                    <i class="icon-magnifier"></i>
                                </button>
                                <input name="query" type="text" class="form-control p-2" placeholder="Szukaj..." value="{{ request()->get('query') }}">
                            </div>
                        </form>
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
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>E-mail</th>
                                <th>Data rejestracji</th>
                                @can(config('permissions_list.NEWSLETTER_DESTROY'))
                                    <th>Działania</th>
                                @endcan
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($newsletters as $newsletter)
                                <tr>
                                    <td>{{ $newsletter->id }}</td>
                                    <td>{{ $newsletter->email }}</td>
                                    <td class="ltr text-right subscribed-at">{{ jalalian()->forge($newsletter->subscribed_at)->format(config('common.datetime_format')) }}</td>
                                    @can(config('permissions_list.NEWSLETTER_DESTROY'))
                                        <td>
                                            <div class="d-flex gap-2">
                                                <x-common-delete-button :route="route(config('app.panel_prefix', 'panel') . '.newsletters.destroy', $newsletter->id)"/>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Display pagination links -->
                    {{ $newsletters->links() }}

                </div><!-- /.portlet-body -->
            </div><!-- /.portlet -->
        </div>
    </div>

@endsection
