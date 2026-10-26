{{-- Comments --}}
@can(config('permissions_list.COMMENT_INDEX', false))
    <div class="col-12">
        <div class="portlet box shadow min-height-500">
            <div class="portlet-heading">
                <div class="portlet-title">
                    <h3 class="title">
                        <i class="icon-bubbles"></i>
                        Ostatnie komentarze
                    </h3>
                </div><!-- /.portlet-title -->
                <div class="buttons-box">
                    <a class="btn btn-sm btn-default btn-round btn-fullscreen" rel="tooltip"
                       aria-label="Pełny ekran" data-bs-original-title="Pełny ekran">
                        <i class="icon-size-fullscreen"></i>
                        <div class="paper-ripple">
                            <div class="paper-ripple__background"></div>
                            <div class="paper-ripple__waves"></div>
                        </div>
                    </a>
                    <a class="btn btn-sm btn-default btn-round btn-close" rel="tooltip"
                       aria-label="Zamknij" data-bs-original-title="Zamknij">
                        <i class="icon-trash"></i>
                        <div class="paper-ripple">
                            <div class="paper-ripple__background"></div>
                            <div class="paper-ripple__waves"></div>
                        </div>
                    </a>
                </div><!-- /.buttons-box -->
            </div><!-- /.portlet-heading -->
            <div class="portlet-body">
                <div class="table-responsive" style="overflow-x: auto !important;">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Treść</th>
                            <th>Autor</th>
                            <th>Konto gościa</th>
                            <th>Status</th>
                            <th>Odpowiedź na</th>
                            <th>Obiekt</th>
                            <th>Data dodania</th>
                            @canany([
                                config('permissions_list.COMMENT_APPROVE', false),
                                config('permissions_list.COMMENT_REJECT', false),
                                config('permissions_list.COMMENT_SHOW', false),
                                config('permissions_list.COMMENT_DESTROY', false)
                            ])
                                <th>Akcje</th>
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
                                <td class="reply">{{ $comment->parent ? "{$comment->parent->commenterName()} (id: {$comment->parent->id})" : 'Brak' }}</td>
                                <td>{{ class_basename($comment->commentable_type) }}</td>
                                <td class="nowrap">{{ jalalian()->forge($comment->created_at)->format(config('common.datetime_format')) }}</td>
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
                                                   rel="tooltip" aria-label="Podgląd komentarza" data-bs-original-title="Podgląd komentarza"
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
            </div><!-- /.portlet-body -->
        </div><!-- /.portlet -->
    </div>
@endcan
