@extends('panel::layouts.master', ['title' => $issue->exists ? 'Edytuj numer gazety' : 'Dodaj numer gazety'])

@section('content')
    <div class="col-12">
        <div class="portlet box shadow">
            <div class="portlet-heading">
                <div class="portlet-title"><h1 class="title">{{ $issue->exists ? 'Edytuj numer gazety' : 'Dodaj numer gazety' }}</h1></div>
            </div>
            <div class="portlet-body">
                <form class="bulletin-upload-form" action="{{ $issue->exists ? route(config('app.panel_prefix', 'panel').'.bulletins.update', $issue) : route(config('app.panel_prefix', 'panel').'.bulletins.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if($issue->exists) @method('PUT') @endif
                    <x-common-error-messages/>
                    <div class="row">
                        <div class="form-group col-md-8">
                            <label for="issue-title">Tytuł numeru</label>
                            <input class="form-control" id="issue-title" name="title" required maxlength="160" value="{{ old('title', $issue->title) }}" placeholder="Np. sŁodziaki — numer 1/2026">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="issue-date">Data wydania</label>
                            <input class="form-control" id="issue-date" name="published_on" type="date" required value="{{ old('published_on', $issue->published_on?->format('Y-m-d')) }}">
                        </div>
                        <div class="form-group col-12">
                            <label for="issue-description">Krótki opis (opcjonalnie)</label>
                            <textarea class="form-control" id="issue-description" name="description" maxlength="500" rows="3">{{ old('description', $issue->description) }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="issue-pdf">Gazeta w PDF</label>
                            <input class="form-control" id="issue-pdf" name="pdf" type="file" accept="application/pdf,.pdf" @required(! $issue->exists) aria-describedby="pdf-help">
                            <p class="help-block" id="pdf-help">PDF do 50 MB. {{ $issue->exists ? 'Pozostaw puste, aby zachować obecny plik.' : '' }}</p>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="issue-cover">Okładka</label>
                            <input class="form-control" id="issue-cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp" @required(! $issue->exists) aria-describedby="cover-help">
                            <p class="help-block" id="cover-help">JPG, PNG lub WebP do 4 MB. Najlepiej pionowa okładka, ok. 800 × 1130 px.</p>
                            @if($issue->cover_path)<img src="{{ $issue->coverUrl() }}" alt="Obecna okładka" width="100" style="height:auto">@endif
                        </div>
                        <div class="form-group col-12">
                            <input type="hidden" name="is_published" value="0">
                            <label for="issue-published"><input type="checkbox" id="issue-published" name="is_published" value="1" @checked(old('is_published', $issue->is_published))> Opublikuj numer</label>
                            <p class="help-block">Numer z przyszłą datą pojawi się w dniu wydania. Odznacz publikację, aby go ukryć.</p>
                        </div>
                    </div>
                    <button class="btn btn-success" type="submit">Zapisz numer</button>
                    <a class="btn btn-default" href="{{ route(config('app.panel_prefix', 'panel').'.bulletins.index') }}">Wróć do wydań</a>
                </form>
            </div>
        </div>
    </div>
@endsection
