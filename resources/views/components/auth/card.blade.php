<div class="auth-card">
    <div class="auth-card-header">
        <div class="auth-mini-badge">{{ __('auth.enterprise_badge') }}</div>

        <h1 class="auth-card-title">{{ $title }}</h1>

        @if (!empty($subtitle))
            <p class="auth-card-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    {{ $slot }}
</div>