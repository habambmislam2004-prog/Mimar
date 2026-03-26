<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Auth\LoginWebRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function showLoginForm(): View
    {
        return $this->create();
    }

    public function store(LoginWebRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt(
            [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ],
            (bool) ($credentials['remember'] ?? false)
        )) {
            return back()
                ->withErrors([
                    'email' => __('messages.invalid_credentials'),
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        return redirect()->route('home');
    }

    public function login(LoginWebRequest $request): RedirectResponse
    {
        return $this->store($request);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function logout(Request $request): RedirectResponse
    {
        return $this->destroy($request);
    }
}