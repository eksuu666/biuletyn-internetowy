@extends('panel::layouts.master', ['title' => 'Wiadomości od czytelników'])

@section('content')

    <x-common-breadcrumbs>
        <li><a>Kontakt</a></li>
        <li><a>Wiadomości od czytelników</a></li>
    </x-common-breadcrumbs>

    <div class="row pe-0">
        <div class="col-12 pe-0">
            <div class="portlet box shadow min-height-500">
                <div class="portlet-heading">
                    <div class="portlet-title d-flex gap-3">
                        <h3 class="title m-0">
                            <i class="icon-envelope"></i>
                            Wiadomości od czytelników
                        </h3>
                        <form class="d-inline-block search-form">
                            <div class="input-group">
                                <button class="btn btn-secondary d-flex align-items-center" type="submit">
                                    <i class="icon-magnifier"></i>
                                </button>
                                <input name="query" type="text" class="form-control p-2" placeholder="Szukaj..." value="{{ request()->get('query') }}">
                                @foreach(request()->except(['query', 'page']) as $key => $value)
                                    <input name="{{ $key }}" type="hidden" value="{{ $value }}">
                                @endforeach
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

                        <!-- Filter box -->
                        <div class="btn-group" rel="tooltip"
                             aria-label="Filtruj komentarze" data-bs-original-title="Filtruj komentarze">
                            <button type="button" class="btn btn-sm btn-default btn-round btn-info text-white dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="true">
                                <i class=" fas fa-filter d-flex justify-content-center align-items-center"></i>
                                <div class="paper-ripple">
                                    <div class="paper-ripple__background"></div>
                                    <div class="paper-ripple__waves"></div>
                                </div>
                            </button>
                            <ul class="dropdown-menu">
                                @foreach($filters as $value)
                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ route(
                                                            config('app.panel_prefix', 'panel') . '.contact-us.messages.index',
                                                            ['filter' => $value] + request()->all()
                                                        ) }}">
                                            {{ __($value) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div><!-- /.buttons-box -->
                </div><!-- /.portlet-heading -->
                <div class="portlet-body">
                    <div class="table-responsive" style="overflow-x: auto !important;">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Temat</th>
                                <th>Nazwa</th>
                                <th>E-mail</th>
                                <th>Numer telefonu</th>
                                <th>Wiadomość</th>
                                <th>Status odczytu</th>
                                <th>Data otrzymania</th>
                                <th>Działania</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($userMessages as $userMessage)
                                <tr>
                                    <td>{{ $userMessage->id }}</td>
                                    <td>{{ $userMessage->subject }}</td>
                                    <td>{{ $userMessage->name }}</td>
                                    <td>{{ $userMessage->email }}</td>
                                    <td>{{ nullable_value($userMessage->phone) }}</td>
                                    <td>{{ str($userMessage->message)->limit(30) }}</td>
                                    <td class="{{ status_class($userMessage->isSeen()) }}">{{ $userMessage->getSeenStatus() }}</td>
                                    <td class="ltr text-right nowrap">{{ jalalian()->forge($userMessage->created_at)->format(config('common.datetime_format')) }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a class="btn btn-sm btn-info btn-icon round d-flex justify-content-center align-items-center"
                                               rel="tooltip" aria-label="Otwórz wiadomość" data-bs-original-title="Otwórz wiadomość"
                                               href="{{ route(config('app.panel_prefix', 'panel') . '.contact-us.messages.show', $userMessage->id) }}">
                                                <i class="icon-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Display pagination links -->
                    {{ $userMessages->links() }}

                </div><!-- /.portlet-body -->
            </div><!-- /.portlet -->
        </div>
    </div>
@endsection
