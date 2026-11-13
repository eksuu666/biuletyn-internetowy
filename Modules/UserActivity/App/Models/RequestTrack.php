<?php

namespace Modules\UserActivity\App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class RequestTrack extends Model
{
    use Searchable;

    public const PAGE_VIEW = 'page_view';

    protected $fillable = [
        'user_track_id',
        'url',
        'referer',
        'tag',
    ];

    public static function getVisitCounts(): array
    {
        $now = CarbonImmutable::now();
        $periods = [
            'hourly' => $now->subHour(),
            'ten_hours' => $now->subHours(10),
            'daily' => $now->subDay(),
            'weekly' => $now->subWeek(),
            'monthly' => $now->subMonth(),
            'yearly' => $now->subYear(),
        ];
        $views = self::query()->toBase()->where('tag', self::PAGE_VIEW)->selectRaw('COUNT(*) AS total');
        foreach ($periods as $period => $start) {
            $views->selectRaw("COUNT(CASE WHEN created_at >= ? THEN 1 END) AS {$period}", [$start]);
        }

        $counts = (array) $views->first();
        $counts['all'] = $counts['total'];
        unset($counts['total']);

        return array_map('intval', $counts);
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'user_track_id' => (int) $this->user_track_id,
            'url' => $this->url,
            'referer' => $this->referer,
            'tag' => $this->tag,
        ];
    }

    public function userTrack(): BelongsTo
    {
        return $this->belongsTo(UserTrack::class);
    }
}
