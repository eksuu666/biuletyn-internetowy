{{-- Articles --}}
@can(config('permissions_list.ARTICLE_INDEX', false))
    <div class="col-12">
        <div class="portlet box shadow min-height-500">
            <div class="portlet-heading">
                <div class="portlet-title">
                    <h3 class="title">
                        <i class="icon-globe"></i>
                        Ostatnie artykuły
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
                <div class="table-responsive overflow-x-auto">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Miniaturka</th>
                            <th>Tytuł</th>
                            <th>Slug</th>
                            <th>Autor</th>
                            <th>Kategoria</th>
                            <th>Tagi</th>
                            <th>Polubienia</th>
                            <th>Data publikacji</th>
                            <th>Data utworzenia</th>
                            <th>Wybór redakcji</th>
                            <th>Gorący temat</th>
                            <th>Status</th>
                            @canany([config('permissions_list.ARTICLE_UPDATE'), config('permissions_list.ARTICLE_DESTROY')])
                                <th>Akcje</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($articles as $article)
                            <tr>
                                <td>{{ $article->id }}</td>
                                <td>
                                    <img src="{{ asset('storage/' . $article->image->file_path) }}" alt="{{ $article->image->alt_text }}" width="100px" style="max-height: 90px">
                                </td>
                                <td>{{ $article->title }}</td>
                                <td>{{ $article->slug }}</td>
                                <td>{{ $article->user->full_name }}</td>
                                <td>{{ $article->category->name }}</td>
                                <td class="nowrap">{{ str(nullable_value($article->tagNames()))->limit(50) }}</td>
                                <td>{{ $article->likeCount }}</td>
                                <td class="nowrap">{{ jalalian()->forge($article->published_at)->format(config('common.datetime_format')) }}</td>
                                <td class="nowrap">{{ jalalian()->forge($article->created_at)->format(config('common.datetime_format')) }}</td>
                                <td class="{{ status_class($article->editor_choice) }}">{{ status_message($article->editor_choice) }}</td>
                                <td class="{{ status_class($article->isHot()) }}">{{ status_message($article->isHot()) }}</td>
                                <td class="{{ status_class($article->status) }}">{{ status_message($article->status) }}</td>
                                @canany([
                                    config('permissions_list.ARTICLE_UPDATE'),
                                    config('permissions_list.ARTICLE_DESTROY'),
                                    config('permissions_list.SEO_MANAGEMENT', false)
                                ])
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can(config('permissions_list.ARTICLE_UPDATE', false))
                                                <a class="btn btn-sm btn-info btn-icon round d-flex justify-content-center align-items-center"
                                                   rel="tooltip" aria-label="Edytuj" data-bs-original-title="Edytuj"
                                                   href="{{ route(config('app.panel_prefix', 'panel') . '.articles.edit', $article->id) }}">
                                                    <i class="icon-pencil"></i>
                                                </a>
                                            @endcan

                                            @can(config('permissions_list.ARTICLE_DESTROY', false))
                                                <x-common-delete-button :route="route(config('app.panel_prefix', 'panel') . '.articles.destroy', $article->id)"/>
                                            @endcan

                                            @can(config('permissions_list.SEO_MANAGEMENT', false))
                                                <x-seo-manager-seo-settings-button :route="route(config('app.panel_prefix', 'panel') . '.articles.seo-settings', $article->id)"/>
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
