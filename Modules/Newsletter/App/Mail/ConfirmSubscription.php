<?php

namespace Modules\Newsletter\App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmSubscription extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string $confirmationUrl) {}

    public function build()
    {
        return $this->subject('Potwierdź zapis do newslettera sŁodziaka')->view('newsletter::emails.confirm');
    }
}
