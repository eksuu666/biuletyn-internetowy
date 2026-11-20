@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-list portal-service">
        @include('front::partials.page-heading', ['title' => 'Działy i tematy', 'eyebrow' => 'Odkrywaj sŁodziaki', 'icon' => 'th-large', 'description' => 'Reportaże, porady i historie. Znajdź coś dla siebie.'])
        <div class="portal-category-grid">
            @forelse($categories as $category)
                <a class="portal-category service-card" href="{{ route('categories.show', $category->slug) }}">
                    <strong>{{ $category->name }}</strong>
                    <span>{{ trans_choice('{0} Brak artykułów|{1} :count artykuł|[2,4] :count artykuły|[5,*] :count artykułów', $category->articles_count) }}</span>
                    <span aria-hidden="true">→</span>
                </a>
            @empty
                <p class="portal-empty">Działy pojawią się tutaj po dodaniu ich przez redakcję.</p>
            @endforelse
        </div>
        <x-front-listing-page-pagination :paginator="$categories"/>
    </div>
@endsection
