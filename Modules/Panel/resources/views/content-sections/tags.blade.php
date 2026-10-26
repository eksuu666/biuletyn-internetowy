{{-- Tags --}}
@can(config('permissions_list.TAG_INDEX', false))
    <div class="col-12">
        <div class="portlet box shadow min-height-500">
            <div class="portlet-heading">
                <div class="portlet-title">
                    <h3 class="title">
                        <i class="icon-tag"></i>
                        Tagi
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
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nazwa</th>
                            <th>Slug</th>
                            <th>Data utworzenia</th>
                            <th>Gorący temat</th>
                            <th>Status</th>
                            @canany([config('permissions_list.TAG_UPDATE'), config('permissions_list.TAG_DESTROY')])
                                <th>Akcje</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tags as $tag)
                            <tr>
                                <td>{{ $tag->id }}</td>
                                <td>{{ $tag->name }}</td>
                                <td>{{ $tag->slug }}</td>
                                <td class="nowrap">{{ jalalian()->forge($tag->created_at)->format(config('common.datetime_format')) }}</td>
                                <td class="{{ status_class($tag->isHot()) }}">{{ status_message($tag->isHot()) }}</td>
                                <td class="{{ status_class($tag->status) }}">{{ status_message($tag->status) }}</td>
                                @canany([
                                    config('permissions_list.TAG_UPDATE'),
                                    config('permissions_list.TAG_DESTROY'),
                                    config('permissions_list.SEO_MANAGEMENT', false)
                                ])
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can(config('permissions_list.TAG_UPDATE', false))
                                                <a class="btn btn-sm btn-info btn-icon round d-flex justify-content-center align-items-center"
                                                   rel="tooltip" aria-label="Edytuj" data-bs-original-title="Edytuj"
                                                   href="{{ route(config('app.panel_prefix', 'panel') . '.tags.edit', $tag->id) }}">
                                                    <i class="icon-pencil"></i>
                                                </a>
                                            @endcan

                                            @can(config('permissions_list.TAG_DESTROY', false))
                                                <x-common-delete-button :route="route(config('app.panel_prefix', 'panel') . '.tags.destroy', $tag->id)"/>
                                            @endcan

                                            @can(config('permissions_list.SEO_MANAGEMENT', false))
                                                <x-seo-manager-seo-settings-button :route="route(config('app.panel_prefix', 'panel') . '.tags.seo-settings', $tag->id)"/>
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
