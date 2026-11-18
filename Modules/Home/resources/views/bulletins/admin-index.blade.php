@extends('panel::layouts.master', ['title' => 'Gazety PDF'])

@section('content')
    <div class="col-12">
        <div class="portlet box shadow">
            <div class="portlet-heading">
                <div class="portlet-title"><h1 class="title">Gazety PDF</h1></div>
                <div class="buttons-box">
                    @can(config('permissions_list.PAGE_STORE'))
                        <a class="btn btn-success" href="{{ route(config('app.panel_prefix', 'panel').'.bulletins.create') }}">Dodaj numer</a>
                    @endcan
                    <a class="btn btn-default" href="{{ route('bulletins.index') }}" target="_blank" rel="noopener">Zobacz wydania ↗</a>
                </div>
            </div>
            <div class="portlet-body">
                @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
                @if($issues->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Okładka</th><th>Numer gazety</th><th>Data wydania</th><th>Publikacja</th><th>Działania</th></tr></thead>
                            <tbody>
                                @foreach($issues as $issue)
                                    <tr>
                                        <td><img src="{{ $issue->coverUrl() }}" alt="" width="54" height="76" style="object-fit:contain"></td>
                                        <td>{{ $issue->title }}</td>
                                        <td>{{ $issue->published_on->format('d.m.Y') }}</td>
                                        <td>{{ ! $issue->is_published ? 'Szkic' : ($issue->published_on->isFuture() ? 'Zaplanowany' : 'Opublikowany') }}</td>
                                        <td>
                                            @can(config('permissions_list.PAGE_UPDATE'))
                                                <a class="btn btn-sm btn-info" href="{{ route(config('app.panel_prefix', 'panel').'.bulletins.edit', $issue) }}">Edytuj</a>
                                            @endcan
                                            @if($issue->is_published && ! $issue->published_on->isFuture())
                                                <a class="btn btn-sm btn-default" href="{{ $issue->pdfUrl() }}" target="_blank" rel="noopener">Otwórz PDF ↗</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $issues->links() }}
                @else
                    <p>Dodaj pierwszy numer: plik PDF i zdjęcie okładki. Po opublikowaniu pojawi się na stronie głównej.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
