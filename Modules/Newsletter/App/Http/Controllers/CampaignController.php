<?php

namespace Modules\Newsletter\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Newsletter\App\Jobs\SendCampaignDelivery;
use Modules\Newsletter\App\Models\Campaign;
use Modules\Newsletter\App\Models\Newsletter;

class CampaignController
{
    public function index()
    {
        $campaigns = Campaign::latest()->paginate(15);
        $confirmedCount = Newsletter::confirmed()->count();
        $deliveryCounts = DB::table('newsletter_deliveries')->selectRaw('campaign_id, status, count(*) as total')->groupBy('campaign_id', 'status')->get()->groupBy('campaign_id');

        return view('newsletter::campaigns', compact('campaigns', 'confirmedCount', 'deliveryCounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['subject' => 'required|string|max:200', 'body' => 'required|string|max:50000']);
        Campaign::create($data + ['user_id' => auth()->id()]);

        return back()->with('success', 'Zapisano szkic newslettera.');
    }

    public function send(Campaign $campaign)
    {
        DB::transaction(function () use ($campaign) {
            $campaign = Campaign::lockForUpdate()->findOrFail($campaign->id);
            abort_unless($campaign->status === 'draft', 409);
            if (! Newsletter::confirmed()->exists()) {
                throw ValidationException::withMessages(['newsletter' => 'Brak potwierdzonych odbiorców.']);
            }
            $campaign->update(['status' => 'queued']);
            Newsletter::confirmed()->chunkById(500, function ($subscribers) use ($campaign) {
                foreach ($subscribers as $subscriber) {
                    $id = DB::table('newsletter_deliveries')->insertGetId(['campaign_id' => $campaign->id, 'newsletter_id' => $subscriber->id, 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()]);
                    SendCampaignDelivery::dispatch($id)->afterCommit();
                }
            });
        });

        return back()->with('success', 'Wydanie przekazano do kolejki.');
    }
}
