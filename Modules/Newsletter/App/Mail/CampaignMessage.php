<?php

namespace Modules\Newsletter\App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Modules\Newsletter\App\Models\Campaign;
use Modules\Newsletter\App\Models\Newsletter;

class CampaignMessage extends Mailable
{
    public function __construct(public Campaign $campaign, public Newsletter $subscriber) {}

    public function build()
    {
        return $this->subject($this->campaign->subject)->view('newsletter::emails.campaign', ['unsubscribeUrl' => URL::signedRoute('newsletters.unsubscribe', $this->subscriber)]);
    }
}
