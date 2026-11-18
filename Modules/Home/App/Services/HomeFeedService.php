<?php

namespace Modules\Home\App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Modules\AdManager\App\Models\Ad;
use Throwable;

class HomeFeedService
{
    public function __construct(private readonly HomeService $homeService) {}

    public function batch(?string $token = null): array
    {
        $state = ['snapshot' => now()->toDateTimeString(), 'exclude' => null, 'last_date' => null, 'last_id' => null, 'ads_offset' => 0];
        if ($token !== null) {
            try {
                $state = json_decode(Crypt::decryptString($token), true, 512, JSON_THROW_ON_ERROR);
                if (! isset($state['snapshot'], $state['last_date'], $state['last_id'], $state['ads_offset'])) {
                    abort(422);
                }
                Carbon::parse($state['last_date']);
            } catch (Throwable) {
                abort(422, 'Odśwież stronę, aby wczytać artykuły.');
            }
        }

        $lead = null;
        if ($token === null) {
            $lead = $this->homeService->baseQuery()->editorChoice()->first() ?? $this->homeService->baseQuery()->first();
            $state['exclude'] = $lead?->id;
        }
        $query = $this->homeService->baseQuery()->where('published_at', '<=', $state['snapshot']);
        if ($state['exclude']) {
            $query->where('articles.id', '!=', $state['exclude']);
        }
        if ($state['last_date']) {
            $query->where(function ($query) use ($state) {
                $query->where('published_at', '<', $state['last_date'])
                    ->orWhere(fn ($query) => $query->where('published_at', $state['last_date'])->where('articles.id', '<', $state['last_id']));
            });
        }
        $limit = $lead ? 11 : 12;
        $rows = $query->limit($limit + 1)->get();
        $hasMore = $rows->count() > $limit;
        $stories = $rows->take($limit)->values();
        $next = null;
        if ($last = $stories->last()) {
            $state['last_date'] = $last->published_at->toDateTimeString();
            $state['last_id'] = $last->id;
        }
        if ($lead) {
            $stories->prepend($lead);
        }
        $items = $stories->map(fn ($story) => [
            'kind' => 'article', 'record' => $story,
            'large' => $story->is_sponsored ? $story->sponsored_card_size === 'large' : ($story->editor_choice || $story->is($lead)),
        ])->all();

        if ($stories->isNotEmpty()) {
            $ad = Ad::with('image')->active()->bySection(Ad::HOMEPAGE_FEED)->has('image')
                ->where('published_at', '<=', $state['snapshot'])->orderBy('id')->skip($state['ads_offset'])->first();
            if ($ad) {
                array_splice($items, min(5, count($items)), 0, [['kind' => 'ad', 'record' => $ad, 'large' => $ad->card_size === 'large']]);
                $state['ads_offset']++;
            }
        }
        // Fill a remaining single-column space before starting a two-column card.
        $ordered = [];
        $used = (int) ($state['grid_used'] ?? 0);
        while ($items !== []) {
            $index = 0;
            if ($used === 2 && $items[0]['large']) {
                foreach ($items as $key => $item) {
                    if (! $item['large']) {
                        $index = $key;
                        break;
                    }
                }
            }
            $item = $items[$index];
            array_splice($items, $index, 1);
            $units = $item['large'] ? 2 : 1;
            $used = $used + $units > 3 ? $units : $used + $units;
            if ($used === 3) {
                $used = 0;
            }
            $ordered[] = $item;
        }
        $state['grid_used'] = $used;
        if ($hasMore) {
            $next = route('home.feed', ['cursor' => Crypt::encryptString(json_encode($state, JSON_THROW_ON_ERROR))]);
        }

        return ['items' => $ordered, 'nextUrl' => $next, 'firstBatch' => $token === null];
    }
}
