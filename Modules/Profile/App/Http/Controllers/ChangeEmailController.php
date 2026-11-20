<?php

namespace Modules\Profile\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Profile\App\Http\Requests\ChangeEmailRequest;
use Modules\Profile\App\Services\ChangeEmailService;

class ChangeEmailController extends Controller
{
    public function __construct(private readonly ChangeEmailService $changeEmailService)
    {
        $this->middleware('can:'.config('permissions_list.PROFILE_CHANGE_EMAIL', false));
    }

    public function changeEmailView(): View
    {
        $user = auth()->user();

        return view($user->isEditorialStaff() ? 'profile::change-email' : 'profile::account', compact('user') + ['accountSection' => 'email']);
    }

    public function sendChangeEmailVerification(ChangeEmailRequest $request): RedirectResponse
    {
        if (config('mail.default') === 'log') {
            return to_route(config('app.panel_prefix').'.profile.email.change')
                ->withErrors('Zmiana adresu e-mail jest obecnie niedostępna.');
        }

        $this->changeEmailService->sendChangeEmailVerification($request);

        return to_route(config('app.panel_prefix', 'panel').'.profile.email.change')
            ->with('success', __('Check your email for a verification link to complete the email change.'));
    }

    public function verifyChangeEmail(Request $request): RedirectResponse
    {
        auth()->user()->update(['email' => $request->new_email]);

        return to_route(config('app.panel_prefix', 'panel').'.profile.email.change')->with('success', __('entity_edited', ['entity' => __('email')]));
    }
}
