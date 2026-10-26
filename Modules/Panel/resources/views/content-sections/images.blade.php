{{-- Images --}}
@can(config('permissions_list.IMAGE_INDEX', false))
    <div class="col-12">
        <div class="portlet box shadow min-height-500">
            <div class="portlet-heading">
                <div class="portlet-title">
                    <h3 class="title">
                        <i class="icon-picture"></i>
                        Ostatnie obrazy
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
                            <th>Miniaturka</th>
                            <th>Ścieżka pliku</th>
                            <th>Tekst alternatywny (Alt)</th>
                            <th>Autor uploadu</th>
                            <th>Data dodania</th>
                            @can('operations', $imageClassName)
                                <th>Akcje</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($images as $image)
                            @can('show', $image)
                                <tr>
                                    <td>{{ $image->id }}</td>
                                    <td>
                                        <img src="{{ asset('storage/' . $image->file_path) }}" alt="{{ $image->alt_text }}" width="100px" style="max-height: 90px">
                                    </td>
                                    <td>{{ $image->file_path }}</td>
                                    <td>{{ nullable_value($image->alt_text) }}</td>
                                    <td>{{ $image->user_full_name }}</td>
                                    <td class="nowrap">{{ jalalian()->forge($image->created_at)->format(config('common.datetime_format')) }}</td>
                                    @can('operations', $imageClassName)
                                        <td>
                                            <div class="d-flex gap-2">
                                                @can('update', $image)
                                                    <a class="btn btn-sm btn-info btn-icon round d-flex justify-content-center align-items-center"
                                                       rel="tooltip" aria-label="Edytuj" data-bs-original-title="Edytuj"
                                                       href="{{ route(config('app.panel_prefix', 'panel') . '.images.edit', $image->id) }}">
                                                        <i class="icon-pencil"></i>
                                                    </a>
                                                @endcan

                                                @can('destroy', $image)
                                                    <x-common-delete-button :route="route(config('app.panel_prefix', 'panel') . '.images.destroy', $image->id)"/>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @endcan
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div><!-- /.portlet-body -->
        </div><!-- /.portlet -->
    </div>
@endcan
