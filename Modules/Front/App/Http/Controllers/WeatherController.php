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
        $city = $request->query('miasto', 'lodz');
        $station = is_string($city) ? $stations->get($city) : null;
        $station ??= $stations->get('lodz') ?? $stations->first();

        SEOTools::setTitle('Pogoda');
        SEOTools::setDescription('Ostatnie pomiary pogody ze stacji IMGW w Polsce.');
        SEOTools::setCanonical(route('weather.index'));

        return view('front::weather.index', compact('station', 'stations'));
    }
}
