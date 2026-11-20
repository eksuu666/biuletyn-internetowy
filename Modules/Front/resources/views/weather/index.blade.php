@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static portal-service">
        @include('front::partials.page-heading', ['title' => 'Pogoda', 'eyebrow' => 'Na cztery łapy', 'icon' => 'sun-o', 'description' => 'Sprawdź warunki, zanim ruszycie na spacer.', 'headingImage' => 'home/illustrations/pogoda.webp'])

        @if($stations->isNotEmpty())
            <div class="service-card portal-weather-toolbar">
                <form class="portal-tools" action="{{ route('weather.index') }}" role="search" aria-label="Wyszukaj pogodę">
                    <label for="weather-city">Miasto
                        <input id="weather-city" name="miasto" type="search" list="weather-cities" maxlength="100" placeholder="Wpisz miasto, np. Łódź" value="{{ $cityInput }}" autocomplete="off" @if($cityError) aria-invalid="true" aria-describedby="weather-city-error" @endif>
                    </label>
                    <datalist id="weather-cities">@foreach($stations as $place)<option value="{{ $place['stacja'] }}"></option>@endforeach</datalist>
                    <button class="portal-button">Sprawdź pogodę</button>
                </form>
                @if($cityError)<p id="weather-city-error" class="portal-weather-error" role="alert">Nie znaleziono stacji. Wybierz miasto z podpowiedzi.</p>@endif
                <nav class="portal-weather-shortcuts" aria-label="Popularne miasta">
                    @foreach(['lodz', 'warszawa', 'krakow', 'wroclaw', 'poznan', 'gdansk'] as $shortcut)
                        @if($stations->has($shortcut))<a href="{{ route('weather.index', ['miasto' => $shortcut]) }}" @if(($station['stacja'] ?? null) === $stations[$shortcut]['stacja']) aria-current="page" @endif>{{ $stations[$shortcut]['stacja'] }}</a>@endif
                    @endforeach
                </nav>
            </div>
        @endif
        <div>
            @if($station)
                <section class="portal-weather service-card" aria-labelledby="weather-heading">
                    <div class="portal-weather-head">
                        <div>
                            <h2 id="weather-heading">{{ $station['stacja'] }}</h2>
                            <p>Ostatni pomiar{{ isset($station['data_pomiaru'], $station['godzina_pomiaru']) ? ' · '.$station['data_pomiaru'].' · '.str_pad($station['godzina_pomiaru'], 2, '0', STR_PAD_LEFT).':00 UTC' : '' }}</p>
                        </div>
                        <div class="portal-weather-temp" aria-label="Temperatura">{{ is_numeric($station['temperatura'] ?? null) ? str_replace('.', ',', $station['temperatura']).' °C' : '-' }}</div>
                    </div>
                    <dl class="portal-weather-metrics">
                        @foreach(['predkosc_wiatru' => ['Wiatr', 'm/s'], 'wilgotnosc_wzgledna' => ['Wilgotność', '%'], 'cisnienie' => ['Ciśnienie', 'hPa']] as $key => [$label, $unit])
                            <div>
                                <dt>{{ $label }}</dt>
                                <dd>{{ is_numeric($station[$key] ?? null) ? str_replace('.', ',', $station[$key]).' '.$unit : '-' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                    <p class="portal-weather-source">Źródłem pochodzenia danych jest <a href="https://danepubliczne.imgw.pl/apiinfo">Instytut Meteorologii i Gospodarki Wodnej - Państwowy Instytut Badawczy</a>.</p>
                </section>
            @else
                @unless($cityError)<p class="service-card portal-service-copy">Pomiary są chwilowo niedostępne.</p>@endunless
            @endif

            <p class="portal-weather-forecast"><a class="portal-service-link" href="https://meteo.imgw.pl/">Prognoza i ostrzeżenia IMGW <span aria-hidden="true">↗</span></a></p>
        </div>
        <section class="service-card portal-weather-map-card" aria-labelledby="weather-map-heading">
            <h2 id="weather-map-heading">Pogoda na mapie</h2>
            <p>Wybierz temperaturę przy mieście, aby zobaczyć pomiary.</p>
            <div id="weather-map" role="region" aria-label="Mapa temperatur w Polsce" tabindex="0"></div>
            <p class="portal-map-status" role="status" hidden>Mapa jest chwilowo niedostępna. Miasto możesz wyszukać powyżej.</p>
            <noscript><p>Włącz JavaScript, aby korzystać z mapy. Pomiary są dostępne powyżej.</p></noscript>
            <script id="weather-map-data" type="application/json">{!! json_encode($mapCities, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
        </section>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('home/vendor/leaflet/leaflet.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('home/vendor/leaflet/leaflet.js') }}" defer></script>
    <script src="{{ asset('home/js/weather-map.js') }}?v={{ filemtime(public_path('home/js/weather-map.js')) }}" defer></script>
@endpush
