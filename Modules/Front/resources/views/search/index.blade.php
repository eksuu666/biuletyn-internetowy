@extends('front::article-list', ['heading' => 'Wyniki wyszukiwania', 'emptyMessage' => 'Nie znaleziono wiadomości. Spróbuj wpisać inną frazę.'])

@section('list-intro')
    <form class="portal-tools portal-search-filters service-card" action="{{ route('search.index') }}" aria-label="Filtry wyników">
        <label for="results-query">Fraza
            <input id="results-query" name="query" type="search" maxlength="200" value="{{ $searchText }}">
        </label>
        <label for="results-category">Dział
            <select id="results-category" name="category">
                <option value="">Wszystkie działy</option>
                @foreach($categories as $item)
                    <option value="{{ $item->slug }}" @selected($category === $item->slug)>{{ $item->name }}</option>
                @endforeach
            </select>
        </label>
        <label for="results-days">Data publikacji
            <select id="results-days" name="days">
                <option value="">Dowolna</option>
                <option value="7" @selected($days === 7)>Ostatnie 7 dni</option>
                <option value="30" @selected($days === 30)>Ostatnie 30 dni</option>
                <option value="365" @selected($days === 365)>Ostatni rok</option>
            </select>
        </label>
        <label for="results-sort">Kolejność
            <select id="results-sort" name="sort">
                <option value="newest" @selected($sort === 'newest')>Od najnowszych</option>
                <option value="oldest" @selected($sort === 'oldest')>Od najstarszych</option>
            </select>
        </label>
        <button class="portal-button"><i class="fa fa-search" aria-hidden="true"></i> Szukaj</button>
    </form>
    <div class="portal-results-meta">
        <p class="portal-search-total">Liczba wyników: <strong>{{ $articles->total() }}</strong></p>
        @if($category !== null || $days || $sort === 'oldest')
            <a href="{{ route('search.index', ['query' => $searchText]) }}">Wyczyść filtry</a>
        @endif
    </div>
@endsection
