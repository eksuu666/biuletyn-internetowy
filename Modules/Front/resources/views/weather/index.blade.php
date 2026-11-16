@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static">
        <a class="portal-back" href="{{ route('home.index') }}">← Strona główna</a>
        <h1>Pogoda</h1>

        @if($station)
            <form class="portal-tools" action="{{ route('weather.index') }}">
                <label for="weather-city">Miejscowość
                    <select id="weather-city" name="miasto">
                        @foreach($stations as $slug => $place)
                            <option value="{{ $slug }}" @selected($station['stacja'] === $place['stacja'])>{{ $place['stacja'] }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="portal-button">Sprawdź pogodę</button>
            </form>

            <section class="portal-weather" aria-labelledby="weather-heading">
                <h2 id="weather-heading">{{ $station['stacja'] }}</h2>
                <p>Ostatni pomiar{{ isset($station['data_pomiaru'], $station['godzina_pomiaru']) ? ' · '.$station['data_pomiaru'].' · '.str_pad($station['godzina_pomiaru'], 2, '0', STR_PAD_LEFT).':00 UTC' : '' }}</p>
                <dl class="portal-weather-metrics">
                    @foreach(['temperatura' => ['Temperatura', '°C'], 'predkosc_wiatru' => ['Wiatr', 'm/s'], 'wilgotnosc_wzgledna' => ['Wilgotność', '%'], 'cisnienie' => ['Ciśnienie', 'hPa']] as $key => [$label, $unit])
                        <div>
                            <dt>{{ $label }}</dt>
                            <dd>{{ is_numeric($station[$key] ?? null) ? str_replace('.', ',', $station[$key]).' '.$unit : '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
                <p class="portal-weather-source">Źródłem pochodzenia danych jest <a href="https://danepubliczne.imgw.pl/apiinfo">Instytut Meteorologii i Gospodarki Wodnej – Państwowy Instytut Badawczy</a>.</p>
            </section>
        @else
            <p class="portal-empty">Pomiary są chwilowo niedostępne.</p>
        @endif

        <p class="portal-weather-links">
            <a class="portal-outline" href="https://meteo.imgw.pl/">Prognoza i ostrzeżenia IMGW</a>
        </p>
    </div>
@endsection
