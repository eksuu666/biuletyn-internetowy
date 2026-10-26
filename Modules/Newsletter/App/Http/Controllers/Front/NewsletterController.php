<?php

namespace Modules\Newsletter\App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Modules\Newsletter\App\Http\Requests\Front\NewsletterRequest;
use Modules\Newsletter\App\Mail\ConfirmSubscription;
use Modules\Newsletter\App\Models\Newsletter;

class NewsletterController extends Controller
{
    public function subscribe(NewsletterRequest $request): RedirectResponse
    {
        $subscriber = Newsletter::firstOrCreate(['email' => strtolower($request->email)]);
        if (! $subscriber->confirmed_at || $subscriber->unsubscribed_at) {
            $token = Str::random(64);
            $subscriber->update(['confirmation_hash' => hash('sha256', $token), 'consented_at' => now(), 'unsubscribed_at' => null, 'confirmed_at' => null]);
            Mail::to($subscriber->email)->queue(new ConfirmSubscription(URL::temporarySignedRoute('newsletters.confirm', now()->addDays(7), ['newsletter' => $subscriber->id, 'token' => $token])));
        }

        return back()->with('success', 'Jeśli zapis wymaga potwierdzenia, wysłaliśmy wiadomość z dalszymi instrukcjami.');
    }

    public function confirm(Newsletter $newsletter, string $token)
    {
        abort_unless($newsletter->confirmation_hash && hash_equals($newsletter->confirmation_hash, hash('sha256', $token)), 403);
        $newsletter->update(['confirmed_at' => now(), 'confirmation_hash' => null, 'unsubscribed_at' => null]);

        return view('newsletter::subscription-status', ['message' => 'Potwierdzono zapis do newslettera.']);
    }

    public function unsubscribeForm(Newsletter $newsletter)
    {
        return view('newsletter::subscription-status', ['message' => 'Czy chcesz wypisać się z newslettera?', 'confirmUnsubscribe' => true]);
    }

    public function unsubscribe(Newsletter $newsletter)
    {
        $newsletter->update(['unsubscribed_at' => now(), 'confirmation_hash' => null]);

        return view('newsletter::subscription-status', ['message' => 'Wypisano z newslettera. Nie będziemy wysyłać kolejnych wydań.']);
    }
}
