<?php

namespace Modules\Front\App\Http\Controllers;

use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WeatherController
{
    private const MAP_CITIES = [
        'lodz' => [51.76, 19.46], 'warszawa' => [52.23, 21.01],
        'krakow' => [50.06, 19.94], 'wroclaw' => [51.11, 17.04],
        'poznan' => [52.41, 16.93], 'gdansk' => [54.35, 18.65],
        'szczecin' => [53.43, 14.55], 'bialystok' => [53.13, 23.16],
        'lublin' => [51.25, 22.57], 'rzeszow' => [50.04, 22.00],
        'katowice' => [50.26, 19.02], 'kielce' => [50.87, 20.63],
        'olsztyn' => [53.78, 20.48], 'torun' => [53.01, 18.60],
        'opole' => [50.67, 17.93], 'zielona-gora' => [51.94, 15.51],
    ];

    public function __invoke(Request $request): View
    {
        $stations = Cache::remember('portal.weather.stations', now()->addMinutes(10), function () {
            try {
                $response = Http::acceptJson()->connectTimeout(2)->timeout(4)
                    ->get('https://danepubliczne.imgw.pl/api/data/synop');

                return $response->ok() ? $response->json() : [];
            } catch (ConnectionException) {
                return [];
            }
        });

        $stations = collect(is_array($stations) ? $stations : [])
            ->filter(fn ($row) => is_array($row) && is_string($row['stacja'] ?? null))
            ->keyBy(fn ($row) => Str::slug($row['stacja']))
            ->sortBy(fn ($row) => Str::ascii($row['stacja']), SORT_NATURAL | SORT_FLAG_CASE);
        $input = $request->query('miasto', '');
        $cityInput = is_string($input) ? mb_substr(trim($input), 0, 100) : '';
        $city = Str::slug($cityInput ?: 'lodz');
        $station = $stations->get($city);
        if ($cityInput === '') {
            $station ??= $stations->first();
        }
        $cityError = $cityInput !== '' && ! $station && $stations->isNotEmpty();
        $mapCities = [];
        foreach (self::MAP_CITIES as $slug => $coordinates) {
            $place = $stations->get($slug);
            if ($place && is_numeric($place['temperatura'] ?? null)) {
                $mapCities[] = [
                    'name' => $place['stacja'], 'coordinates' => $coordinates,
                    'temperature' => str_replace('.', ',', $place['temperatura']).' °C',
                    'url' => route('weather.index', ['miasto' => $slug]),
                    'selected' => ($station['stacja'] ?? null) === $place['stacja'],
                ];
            }
        }

        SEOTools::setTitle('Pogoda');
        SEOTools::setDescription('Ostatnie pomiary pogody ze stacji IMGW w Polsce.');
        SEOTools::setCanonical(route('weather.index'));

        return view('front::weather.index', compact('station', 'stations', 'mapCities', 'cityInput', 'cityError'));
    }
}
