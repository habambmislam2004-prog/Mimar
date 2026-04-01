<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OtpLoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
        ]);

        Log::info('OTP login started', [
            'entered_phone' => $request->phone,
        ]);

        $normalizedPhone = $this->normalizePhone($validated['phone']);

        Log::info('Phone normalized', [
            'normalized_phone' => $normalizedPhone,
        ]);

        $allUsers = User::query()->get(['id', 'name', 'phone', 'email']);

        $user = $allUsers->first(function ($user) use ($normalizedPhone) {
            return $this->normalizePhone($user->phone) === $normalizedPhone;
        });

        Log::info('Matched user result', [
            'user_found' => $user ? true : false,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_phone' => $user?->phone,
            'user_email' => $user?->email,
        ]);

        if (! $user) {
            throw ValidationException::withMessages([
                'phone' => app()->getLocale() === 'ar'
                    ? 'هذا الرقم غير موجود.'
                    : 'This phone number does not exist.',
            ]);
        }

        $code = '123456';

        UserOtp::query()
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->delete();

        UserOtp::query()->create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        Log::info('OTP stored successfully', [
            'user_id' => $user->id,
            'code' => $code,
        ]);

        return redirect()
            ->route('otp.verify.form', ['phone' => $user->phone])
            ->with(
                'success',
                app()->getLocale() === 'ar'
                    ? 'تم إرسال رمز التحقق. رمز التجربة هو 123456'
                    : 'Verification code sent. Test code is 123456'
            );
    }

    public function showVerifyForm(Request $request): View
    {
        return view('auth.otp-verify', [
            'phone' => $request->get('phone'),
        ]);
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        Log::info('OTP verification started', [
            'phone' => $validated['phone'],
            'code' => $validated['code'],
        ]);

        $normalizedPhone = $this->normalizePhone($validated['phone']);

        $user = User::query()->get(['id', 'name', 'phone', 'email'])->first(function ($user) use ($normalizedPhone) {
            return $this->normalizePhone($user->phone) === $normalizedPhone;
        });

        Log::info('Verify matched user', [
            'user_found' => $user ? true : false,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
        ]);

        if (! $user) {
            throw ValidationException::withMessages([
                'phone' => app()->getLocale() === 'ar'
                    ? 'المستخدم غير موجود.'
                    : 'User not found.',
            ]);
        }

        $otp = UserOtp::query()
            ->where('user_id', $user->id)
            ->where('code', $validated['code'])
            ->whereNull('used_at')
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        Log::info('OTP lookup result', [
            'user_id' => $user->id,
            'otp_found' => $otp ? true : false,
        ]);

        if (! $otp) {
            throw ValidationException::withMessages([
                'code' => app()->getLocale() === 'ar'
                    ? 'رمز التحقق غير صحيح أو منتهي الصلاحية.'
                    : 'Invalid or expired verification code.',
            ]);
        }

        $otp->update([
            'used_at' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        Log::info('User logged in successfully after OTP verification', [
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);

        return redirect()
            ->route('home')
            ->with(
                'success',
                app()->getLocale() === 'ar'
                    ? 'تم تسجيل الدخول بنجاح.'
                    : 'Logged in successfully.'
            );
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out');

        return redirect()
            ->route('login')
            ->with(
                'success',
                app()->getLocale() === 'ar'
                    ? 'تم تسجيل الخروج بنجاح.'
                    : 'Logged out successfully.'
            );
    }

    private function normalizePhone(?string $phone): string
    {
        if (! $phone) {
            return '';
        }

        return preg_replace('/\D+/', '', trim($phone));
    }
}