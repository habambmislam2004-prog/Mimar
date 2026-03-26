@extends('layouts.guest')

@section('content')
    <x-auth.card
        :title="app()->getLocale() === 'ar' ? 'الدخول إلى مساحة العمل' : 'Access your workspace'"
        :subtitle="app()->getLocale() === 'ar'
            ? 'سجل الدخول للوصول إلى الخدمات، الطلبات، المحادثات، والتقدير الذكي ضمن تجربة موحدة.'
            : 'Sign in to access services, requests, conversations, and smart estimation in one unified flow.'"
    >
        <div class="auth-switch">
            <a href="{{ route('login') }}" class="active">
                {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign in' }}
            </a>
            <a href="{{ route('register') }}">
                {{ app()->getLocale() === 'ar' ? 'إنشاء حساب' : 'Register' }}
            </a>
        </div>

        @if (session('status'))
            <div class="status-alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <x-auth.input
                :label="app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email address'"
                name="email"
                type="email"
                :placeholder="app()->getLocale() === 'ar' ? 'name@example.com' : 'name@example.com'"
                icon="✉"
            />

            <x-auth.input
                :label="app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password'"
                name="password"
                type="password"
                :placeholder="app()->getLocale() === 'ar' ? 'أدخل كلمة المرور' : 'Enter your password'"
                icon="•"
            />

            <div class="auth-row">
                <label class="checkbox-inline">
                    <input type="checkbox" name="remember" value="1">
                    <span>{{ app()->getLocale() === 'ar' ? 'تذكرني' : 'Remember me' }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="auth-link" href="{{ route('password.request') }}">
                        {{ app()->getLocale() === 'ar' ? 'نسيت كلمة المرور؟' : 'Forgot password?' }}
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-primary">
                {{ app()->getLocale() === 'ar' ? 'دخول إلى المنصة' : 'Access platform' }}
            </button>
        </form>

        <div class="auth-micro">
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'واجهة أوضح' : 'Clearer flow' }}</span>
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'ثنائي اللغة' : 'Bilingual' }}</span>
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'قياس ثابت' : 'Persistent device size' }}</span>
        </div>

        <div class="auth-divider">
            <div class="auth-footer">
                {{ app()->getLocale() === 'ar' ? 'ليس لديك حساب؟' : "Don't have an account?" }}
                <a href="{{ route('register') }}">
                    {{ app()->getLocale() === 'ar' ? 'إنشاء حساب جديد' : 'Create new account' }}
                </a>
            </div>
        </div>
    </x-auth.card>
@endsection