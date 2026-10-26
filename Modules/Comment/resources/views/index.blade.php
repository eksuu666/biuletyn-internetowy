@extends('panel::layouts.master', ['title' => 'Komentarze'])

@section('content')

    <x-common-breadcrumbs>
        <li><a>Komentarze</a></li>
    </x-common-breadcrumbs>

    <div class="row pe-0">
        <div class="col-12 pe-0">
            <div class="portlet box shadow min-height-500">
                <div class="portlet-heading">
                    <div class="portlet-title d-flex gap-3">
                        <h3 class="title m-0">
                            <i class="icon-bubbles"></i>
                            Komentarze
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
                                           href="{{ route(config('app.panel_prefix', 'panel') . '.comments.index', ['filter' => $value] + request()->all()) }}">
                                            {{ __($value) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div><!-- /.buttons-box -->
                </div><!-- /.portlet-heading -->
                <div class="portlet-body">
                    <div class="table-responsive overflow-x-auto">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Treść komentarza</th>
                                <th>Autor komentarza</th>
                                <th>Gość</th>
                                <th>Stan komentarza</th>
                                <th>Odpowiedź</th>
                                <th>Typ materiału</th>
                                <th>Data utworzenia</th>
                                @canany([
                                    config('permissions_list.COMMENT_APPROVE', false),
                                    config('permissions_list.COMMENT_REJECT', false),
                                    config('permissions_list.COMMENT_SHOW', false),
                                    config('permissions_list.COMMENT_DESTROY', false)
                                ])
                                    <th>Działania</th>
                                @endcanany
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($comments as $comment)
                                <tr>
                                    <td>{{ $comment->id }}</td>
                                    <td>{{ str($comment->comment)->limit(20) }}</td>
                                    <td>{{ $comment->commenterName() }}</td>
                                    <td class="{{ status_class(!$comment->isGuest()) }}">{{ $comment->isGuest() ? 'Tak' : 'Nie' }}</td>
                                    <td class="{{ $comment->setStatusClass() }} status">{{ $comment->getStatus() }}</td>
                                    <td class="reply">{{ $comment->parent ? "{$comment->parent->commenterName()} (id: {$comment->parent->id})" : 'Nie' }}</td>
                                    <td>{{ $comment->commentable_type }}</td>
                                    <td class="ltr text-right nowrap">{{ jalalian()->forge($comment->created_at)->format(config('common.datetime_format')) }}</td>
                                    @canany([
                                        config('permissions_list.COMMENT_APPROVE', false),
                                        config('permissions_list.COMMENT_REJECT', false),
                                        config('permissions_list.COMMENT_SHOW', false),
                                        config('permissions_list.COMMENT_DESTROY', false)
                                    ])
                                        <td>
                                            <div class="d-flex gap-2">
                                                @can(config('permissions_list.COMMENT_APPROVE', false))
                                                    @if ($comment->status !== get_class($comment)::APPROVED)
                                                        <form action="{{ route(config('app.panel_prefix', 'panel') . '.comments.approve', $comment->id) }}" method="post">
                                                            @csrf
                                                            @method('patch')
                                                            <button class="btn btn-sm btn-success btn-icon round d-flex justify-content-center align-items-center"
                                                                    rel="tooltip" aria-label="Zatwierdź komentarz" data-bs-original-title="Zatwierdź komentarz">
                                                                <i class="icon-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                                @can(config('permissions_list.COMMENT_REJECT', false))
                                                    @if ($comment->status !== get_class($comment)::REJECTED)
                                                        <form action="{{ route(config('app.panel_prefix', 'panel') . '.comments.reject', $comment->id) }}" method="post">
                                                            @csrf
                                                            @method('patch')
                                                            <button class="btn btn-sm btn-warning btn-icon round d-flex justify-content-center align-items-center"
                                                                    rel="tooltip" aria-label="Odrzuć komentarz" data-bs-original-title="Odrzuć komentarz">
                                                                <i class="icon-close"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                                @can(config('permissions_list.COMMENT_SHOW', false))
                                                    <a class="btn btn-sm btn-info btn-icon round d-flex justify-content-center align-items-center"
                                                       rel="tooltip" aria-label="Zobacz komentarz" data-bs-original-title="Zobacz komentarz"
                                                       href="{{ route(config('app.panel_prefix', 'panel') . '.comments.show', $comment->id) }}">
                                                        <i class="icon-eye"></i>
                                                    </a>
                                                @endcan
                                                @can(config('permissions_list.COMMENT_DESTROY', false))
                                                    <x-common-delete-button :route="route(config('app.panel_prefix', 'panel') . '.comments.destroy', $comment->id)"/>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Display pagination links -->
                    {{ $comments->links() }}

                </div><!-- /.portlet-body -->
            </div><!-- /.portlet -->
        </div>
    </div>

@endsection
