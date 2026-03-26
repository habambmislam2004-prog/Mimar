<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Auth\RegisterWebRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function showRegistrationForm(): View
    {
        return $this->create();
    }

    public function store(RegisterWebRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'account_type' => $data['account_type'],
        ]);

        event(new Registered($user));
        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        if ($user->account_type === 'business') {
            return redirect()->route('business-account.create')
                ->with('success', __('messages.registered_successfully'));
        }

        return redirect()->route('home')
            ->with('success', __('messages.registered_successfully'));
    }

    public function register(RegisterWebRequest $request): RedirectResponse
    {
        return $this->store($request);
    }
}