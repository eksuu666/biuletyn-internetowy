@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-list portal-service forum">
        @include('front::partials.page-heading', ['title' => 'Forum sŁodziaków', 'eyebrow' => 'Miejsce na rozmowę', 'icon' => 'comments-o', 'description' => 'Wybierz dział, zadaj pytanie albo podziel się swoim doświadczeniem.'])
        <div class="portal-page-content">
        <ul class="forum-sections" id="forum-sections" aria-label="Działy forum">
            @foreach($sections as $slug => $section)
                <li>
                    <a href="{{ route('forum.sections.show', $slug) }}">
                        <img src="{{ asset('home/forum/'.$slug.'-rysunek.webp') }}" width="1280" height="640" alt="">
                        <div class="forum-section-copy">
                            <h2>{{ $section['name'] }}</h2>
                            <span class="forum-topic-count">{{ trans_choice('{0} Dołącz do działu|{1} 1 temat|[2,4] :count tematy|[5,*] :count tematów', $counts[$slug] ?? 0) }}</span>
                        </div>
                        <span class="forum-card-arrow" aria-hidden="true">→</span>
                    </a>
                </li>
            @endforeach
        </ul>
        @if($latest->isNotEmpty())
            <section class="forum-recent" aria-labelledby="forum-recent-heading">
                <h2 id="forum-recent-heading">Ostatnio na forum</h2>
                @include('front::forum.topic-list', ['topics' => $latest, 'showSection' => true])
            </section>
        @endif
        </div>
    </div>
@endsection
