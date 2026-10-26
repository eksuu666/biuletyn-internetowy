<?php

namespace Modules\Newsletter\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Newsletter\App\Mail\CampaignMessage;
use Modules\Newsletter\App\Models\Campaign;
use Modules\Newsletter\App\Models\Newsletter;

class SendCampaignDelivery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public function __construct(public int $deliveryId) {}

    public function handle(): void
    {
        $campaignId = DB::transaction(function () {
            $delivery = DB::table('newsletter_deliveries')->where('id', $this->deliveryId)->lockForUpdate()->first();
            if (! $delivery || $delivery->status !== 'pending') {
                return null;
            }
            $subscriber = Newsletter::confirmed()->find($delivery->newsletter_id);
            if ($subscriber) {
                Mail::to($subscriber->email)->send(new CampaignMessage(Campaign::findOrFail($delivery->campaign_id), $subscriber));
            }
            DB::table('newsletter_deliveries')->where('id', $this->deliveryId)->update(['status' => $subscriber ? 'sent' : 'skipped', 'sent_at' => $subscriber ? now() : null, 'updated_at' => now()]);

            return $delivery->campaign_id;
        });
        if ($campaignId) {
            self::finish($campaignId);
        }
    }

    public function failed(?\Throwable $exception): void
    {
        $delivery = DB::table('newsletter_deliveries')->find($this->deliveryId);
        if (! $delivery) {
            return;
        }
        DB::table('newsletter_deliveries')->where('id', $this->deliveryId)->update(['status' => 'failed', 'updated_at' => now()]);
        self::finish($delivery->campaign_id);
    }

    private static function finish(int $campaignId): void
    {
        $deliveries = DB::table('newsletter_deliveries')->where('campaign_id', $campaignId);
        if (! (clone $deliveries)->where('status', 'pending')->exists()) {
            Campaign::whereKey($campaignId)->update(['status' => (clone $deliveries)->where('status', 'failed')->exists() ? 'failed' : 'sent']);
        }
    }
}
