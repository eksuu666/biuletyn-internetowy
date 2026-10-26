<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortalCheck extends Command
{
    protected $signature = 'portal:check';

    protected $description = 'Check database, cache, media storage, queue and failed jobs';

    public function handle(): int
    {
        $key = 'portal-check-'.Str::uuid();
        $file = '.health/'.$key;
        try {
            DB::select('select 1');
            $this->info('Database: OK');
            Cache::put($key, 'ok', 30);
            if (Cache::get($key) !== 'ok') {
                throw new \RuntimeException('Cache readback failed');
            }
            $this->info('Cache: OK');
            Storage::disk('public')->put($file, 'ok');
            if (Storage::disk('public')->get($file) !== 'ok') {
                throw new \RuntimeException('Storage readback failed');
            }
            $this->info('Storage: OK');
            $size = Queue::size();
            $this->info('Pending jobs: '.$size);
            $failed = DB::table('failed_jobs')->count();
            $failed ? $this->warn('Failed jobs: '.$failed) : $this->info('Failed jobs: 0');

            return $failed || $size > 100 ? self::FAILURE : self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        } finally {
            try {
                Cache::forget($key);
                Storage::disk('public')->delete($file);
            } catch (\Throwable) {
            }
        }
    }
}
