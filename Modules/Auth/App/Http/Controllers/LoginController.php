<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Auth\App\Exceptions\FailedLoginException;
use Modules\Auth\App\Http\Requests\LoginRequest;
use Modules\Auth\App\Services\LoginService;

class LoginController extends Controller
{
    public function view(): View
    {
        return view('auth::login');
    }

    public function login(LoginRequest $request, LoginService $loginService): RedirectResponse
    {
        try {
            $loginService->login($request);
            $request->session()->regenerate();

            return to_route($request->user()->can('article::index') ? config('app.panel_prefix').'.articles.index' : 'home.index')->with('success', __('auth::messages.login_success'));
        } catch (FailedLoginException $e) {
            return back()->withErrors($e->getMessage());
        }
    }
}
