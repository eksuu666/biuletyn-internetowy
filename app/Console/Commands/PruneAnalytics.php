<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneAnalytics extends Command
{
    protected $signature = 'portal:prune-analytics {--days= : Retention in days} {--pretend : Report without deleting}';

    protected $description = 'Remove old request and IP analytics without changing editorial content';

    public function handle(): int
    {
        $days = filter_var($this->option('days') ?? config('portal.analytics_retention_days'), FILTER_VALIDATE_INT);
        if (! $days || $days < 1) {
            $this->error('Retention must be at least one day');

            return self::FAILURE;
        }
        $requests = DB::table('request_tracks')->where('created_at', '<', now()->subDays($days));
        $tracks = DB::table('user_tracks')->where('updated_at', '<', now()->subDays($days))->whereNotExists(function ($query) use ($days) {
            $query->selectRaw('1')->from('request_tracks')->whereColumn('user_track_id', 'user_tracks.id')->where('created_at', '>=', now()->subDays($days));
        });
        $this->info('Old requests: '.$requests->count().'; old IP records: '.$tracks->count());
        if (! $this->option('pretend')) {
            DB::transaction(function () use ($requests, $tracks) {
                $requests->delete();
                $tracks->delete();
            });
        }

        return self::SUCCESS;
    }
}
