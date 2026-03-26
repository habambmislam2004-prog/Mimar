@extends('layouts.guest')

@section('content')
    <x-auth.card
        :title="app()->getLocale() === 'ar' ? 'إنشاء حساب جديد' : 'Create your account'"
        :subtitle="app()->getLocale() === 'ar'
            ? 'أنشئ حسابك للوصول إلى الخدمات، الطلبات، المحادثات، والتقدير الذكي ضمن تجربة موحدة.'
            : 'Create your account to access services, requests, conversations, and smart estimation in one unified flow.'"
    >
        <div class="auth-switch">
            <a href="{{ route('login') }}">
                {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign in' }}
            </a>
            <a href="{{ route('register') }}" class="active">
                {{ app()->getLocale() === 'ar' ? 'إنشاء حساب' : 'Register' }}
            </a>
        </div>

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">
                    {{ app()->getLocale() === 'ar' ? 'نوع الحساب' : 'Account type' }}
                </label>

                <select name="account_type" class="form-control custom-input" required>
                    <option value="user">
                        {{ app()->getLocale() === 'ar' ? 'مستخدم عادي' : 'Regular User' }}
                    </option>
                    <option value="business">
                        {{ app()->getLocale() === 'ar' ? 'مستخدم أعمال' : 'Business User' }}
                    </option>
                </select>

                @error('account_type')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <x-auth.input
                :label="app()->getLocale() === 'ar' ? 'الاسم الكامل' : 'Full name'"
                name="name"
                :placeholder="app()->getLocale() === 'ar' ? 'أدخل الاسم الكامل' : 'Enter your full name'"
                icon="👤"
            />

            <x-auth.input
                :label="app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email address'"
                name="email"
                type="email"
                :placeholder="app()->getLocale() === 'ar' ? 'name@example.com' : 'name@example.com'"
                icon="✉"
            />

            <div class="auth-grid-2">
                <x-auth.input
                    :label="app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password'"
                    name="password"
                    type="password"
                    :placeholder="app()->getLocale() === 'ar' ? 'أدخل كلمة المرور' : 'Enter your password'"
                    icon="•"
                />

                <x-auth.input
                    :label="app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm password'"
                    name="password_confirmation"
                    type="password"
                    :placeholder="app()->getLocale() === 'ar' ? 'أعد إدخال كلمة المرور' : 'Re-enter your password'"
                    icon="•"
                />
            </div>

            <button type="submit" class="btn-primary">
                {{ app()->getLocale() === 'ar' ? 'إنشاء الحساب' : 'Create account' }}
            </button>
        </form>

        <div class="auth-micro">
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'خدمات' : 'Services' }}</span>
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'طلبات' : 'Requests' }}</span>
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'تقدير ذكي' : 'Estimation' }}</span>
        </div>

        <div class="auth-divider">
            <div class="auth-footer">
                {{ app()->getLocale() === 'ar' ? 'لديك حساب بالفعل؟' : 'Already have an account?' }}
                <a href="{{ route('login') }}">
                    {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign in' }}
                </a>
            </div>
        </div>
    </x-auth.card>
@endsection