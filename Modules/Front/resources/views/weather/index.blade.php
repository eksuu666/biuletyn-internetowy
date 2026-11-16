@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static portal-service">
        <a class="portal-back" href="{{ route('home.index') }}">← Strona główna</a>
        <h1>Pogoda</h1>

        <div class="portal-service-grid">
            @if($station)
                <section class="portal-weather service-card" aria-labelledby="weather-heading">
                    <div class="portal-weather-head">
                        <div>
                            <h2 id="weather-heading">{{ $station['stacja'] }}</h2>
                            <p>Ostatni pomiar{{ isset($station['data_pomiaru'], $station['godzina_pomiaru']) ? ' · '.$station['data_pomiaru'].' · '.str_pad($station['godzina_pomiaru'], 2, '0', STR_PAD_LEFT).':00 UTC' : '' }}</p>
                        </div>
                        <div class="portal-weather-temp" aria-label="Temperatura">{{ is_numeric($station['temperatura'] ?? null) ? str_replace('.', ',', $station['temperatura']).' °C' : '—' }}</div>
                    </div>
                    <dl class="portal-weather-metrics">
                        @foreach(['predkosc_wiatru' => ['Wiatr', 'm/s'], 'wilgotnosc_wzgledna' => ['Wilgotność', '%'], 'cisnienie' => ['Ciśnienie', 'hPa']] as $key => [$label, $unit])
                            <div>
                                <dt>{{ $label }}</dt>
                                <dd>{{ is_numeric($station[$key] ?? null) ? str_replace('.', ',', $station[$key]).' '.$unit : '—' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                    <p class="portal-weather-source">Źródłem pochodzenia danych jest <a href="https://danepubliczne.imgw.pl/apiinfo">Instytut Meteorologii i Gospodarki Wodnej – Państwowy Instytut Badawczy</a>.</p>
                </section>
            @else
                <p class="service-card portal-service-copy">Pomiary są chwilowo niedostępne.</p>
            @endif

            <aside class="service-card portal-service-copy">
                @if($station)
                    <form class="portal-tools portal-tools-stack" action="{{ route('weather.index') }}">
                        <label for="weather-city">Miejscowość
                            <select id="weather-city" name="miasto">
                                @foreach($stations as $slug => $place)
                                    <option value="{{ $slug }}" @selected($station['stacja'] === $place['stacja'])>{{ $place['stacja'] }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button class="portal-button">Sprawdź pogodę</button>
                    </form>
                @endif
                <a class="portal-service-link" href="https://meteo.imgw.pl/">Prognoza i ostrzeżenia IMGW <span aria-hidden="true">↗</span></a>
            </aside>
        </div>
    </div>
@endsection
