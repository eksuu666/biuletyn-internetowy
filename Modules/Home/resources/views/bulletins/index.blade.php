@extends('front::layouts.master')

@push('styles')
    <link rel="stylesheet" href="{{ asset('home/css/bulletins.css') }}?v={{ filemtime(public_path('home/css/bulletins.css')) }}">
@endpush

@section('content')
    <div class="portal-wrap portal-list print-library">
        @include('front::partials.page-heading', ['title' => 'Z prasy do sieci!', 'eyebrow' => 'Na papierze i w sieci', 'icon' => 'newspaper-o', 'description' => 'sŁodziaki w papierowym wydaniu. Wybierz numer i czytaj bezpłatnie w PDF.'])
        @if($issues->isNotEmpty())
            <div class="print-library-grid">
                @foreach($issues as $issue)
                    <article class="print-issue">
                        <a class="print-cover-stage" href="{{ $issue->pdfUrl() }}" target="_blank" rel="noopener" aria-label="Czytaj {{ $issue->title }} (PDF, nowa karta)">
                            <img class="print-cover" src="{{ $issue->coverUrl() }}" alt="Okładka: {{ $issue->title }}" width="200" height="283" loading="lazy">
                            <span class="print-pdf">PDF <span aria-hidden="true">↗</span></span>
                        </a>
                        <div class="print-info">
                            <time datetime="{{ $issue->published_on->toDateString() }}">{{ $issue->published_on->locale('pl')->translatedFormat('j F Y') }}</time>
                            <h2>{{ $issue->title }}</h2>
                            @if($issue->description)<p>{{ $issue->description }}</p>@endif
                            <a class="print-read" href="{{ $issue->pdfUrl() }}" target="_blank" rel="noopener">Czytaj numer <span aria-hidden="true">↗</span><span class="sr-only"> (PDF, nowa karta)</span></a>
                        </div>
                    </article>
                @endforeach
            </div>
            <x-front-listing-page-pagination :paginator="$issues"/>
        @else
            <div class="print-library-empty">
                @include('home::bulletins.shelf', ['issues' => $issues])
            </div>
        @endif
    </div>
@endsection
