<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? "Mi'mar" }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap');

        :root {
            --bg: #f6f8fc;
            --bg-soft: #eef3fb;
            --surface: rgba(255,255,255,0.82);
            --surface-strong: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --line: rgba(15,23,42,0.08);

            --primary: #1f4fd6;
            --primary-dark: #112f7c;
            --primary-soft: rgba(31,79,214,0.08);

            --ink: #0f172a;
            --ink-soft: #1e293b;
            --gold: #c9a45f;

            --radius-xl: 32px;
            --radius-lg: 24px;
            --radius-md: 18px;

            --shadow-main: 0 28px 80px rgba(15,23,42,0.12);
            --shadow-soft: 0 14px 34px rgba(15,23,42,0.07);
        }

        * { box-sizing: border-box; }
        html, body { min-height: 100%; }

        body {
            margin: 0;
            color: var(--text);
            font-family: "Tajawal", "Inter", sans-serif;
            background:
                radial-gradient(circle at 10% 12%, rgba(31,79,214,0.08), transparent 18%),
                radial-gradient(circle at 84% 18%, rgba(201,164,95,0.10), transparent 16%),
                radial-gradient(circle at 20% 84%, rgba(15,23,42,0.05), transparent 18%),
                linear-gradient(180deg, var(--bg) 0%, var(--bg-soft) 100%);
            overflow-x: hidden;
        }

        html[dir="ltr"] body {
            font-family: "Inter", "Tajawal", sans-serif;
        }

        a, button, input {
            font-family: inherit;
        }

        .preview-shell {
            min-height: 100vh;
        }

        .preview-frame {
            width: 100%;
            margin: 0 auto;
            transition: max-width .25s ease;
        }

        .preview-frame[data-device="desktop"] { max-width: 100%; }
        .preview-frame[data-device="laptop"] { max-width: 1280px; }
        .preview-frame[data-device="ipad-pro"] { max-width: 1024px; }
        .preview-frame[data-device="ipad-mini"] { max-width: 768px; }
        .preview-frame[data-device="iphone-14-pro-max"] { max-width: 430px; }
        .preview-frame[data-device="iphone-12-pro"] { max-width: 390px; }
        .preview-frame[data-device="pixel-7"] { max-width: 412px; }
        .preview-frame[data-device="galaxy-s20-ultra"] { max-width: 412px; }

        .auth-page {
            min-height: 100vh;
            padding: 22px;
            position: relative;
            overflow: hidden;
        }

        .auth-page::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(31,79,214,0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(31,79,214,0.035) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,.18), rgba(0,0,0,.55), rgba(0,0,0,.18));
        }

        .auth-topbar {
            max-width: 1180px;
            margin: 0 auto 22px;
            position: relative;
            z-index: 4;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }

        .brand-mark {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, #101827 0%, #1f2937 100%);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 18px;
            font-weight: 800;
            box-shadow: 0 12px 28px rgba(17,24,39,0.16);
        }

        .brand-name {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .brand-sub {
            margin: 2px 0 0;
            color: var(--muted);
            font-size: 12px;
        }

        .topbar-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .topbar-btn {
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(15,23,42,0.08);
            box-shadow: 0 8px 20px rgba(15,23,42,0.04);
            text-decoration: none;
            color: #24304d;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .auth-stage {
            max-width: 1180px;
            margin: 0 auto;
            min-height: calc(100vh - 126px);
            position: relative;
            display: grid;
            place-items: center;
        }

        .ghost-layer {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .ghost-panel {
            position: absolute;
            border-radius: 28px;
            background: rgba(255,255,255,0.28);
            border: 1px solid rgba(255,255,255,0.38);
            backdrop-filter: blur(14px);
            box-shadow: 0 18px 50px rgba(15,23,42,0.06);
            overflow: hidden;
        }

        .ghost-panel.one {
            width: 300px;
            height: 170px;
            top: 34px;
            inset-inline-start: 18px;
            transform: rotate(-10deg);
        }

        .ghost-panel.two {
            width: 280px;
            height: 160px;
            top: 60px;
            inset-inline-end: 40px;
            transform: rotate(8deg);
        }

        .ghost-panel.three {
            width: 320px;
            height: 180px;
            bottom: 34px;
            inset-inline-end: 80px;
            transform: rotate(-6deg);
        }

        .ghost-header {
            height: 42px;
            background: rgba(15,23,42,0.04);
            border-bottom: 1px solid rgba(15,23,42,0.05);
        }

        .ghost-body {
            padding: 16px;
        }

        .ghost-line {
            height: 10px;
            border-radius: 999px;
            background: rgba(15,23,42,0.08);
            margin-bottom: 10px;
        }

        .ghost-line.long { width: 84%; }
        .ghost-line.mid { width: 58%; }
        .ghost-line.short { width: 34%; }

        .auth-card {
            position: relative;
            z-index: 3;
            width: min(100%, 470px);
            background: var(--surface);
            border: 1px solid rgba(255,255,255,0.58);
            backdrop-filter: blur(18px);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-main);
            padding: 34px;
            overflow: hidden;
        }

        .auth-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, #1f4fd6 0%, #c9a45f 100%);
        }

        .auth-card::after {
            content: "";
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(31,79,214,0.08), transparent 68%);
            top: -60px;
            inset-inline-end: -60px;
            pointer-events: none;
        }

        .auth-card-header {
            position: relative;
            z-index: 1;
            margin-bottom: 22px;
        }

        .auth-mini-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(31,79,214,0.08);
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 700;
        }

        .auth-card-title {
            margin: 0 0 10px;
            font-size: 34px;
            line-height: 1.08;
            letter-spacing: -0.03em;
            font-weight: 800;
            color: #171717;
        }

        .auth-card-subtitle {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.9;
        }

        .auth-switch {
            position: relative;
            z-index: 1;
            margin-bottom: 20px;
            display: inline-grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            padding: 6px;
            border-radius: 999px;
            background: rgba(17,24,39,0.04);
            border: 1px solid rgba(17,24,39,0.06);
        }

        .auth-switch a {
            min-width: 118px;
            height: 42px;
            padding: 0 16px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #6b7280;
            font-size: 13px;
            font-weight: 700;
        }

        .auth-switch a.active {
            background: linear-gradient(135deg, #1f4fd6 0%, #112f7c 100%);
            color: #fff;
            box-shadow: 0 14px 28px rgba(17,47,124,0.18);
        }

        .form-group {
            position: relative;
            z-index: 1;
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #1f2937;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            inset-inline-start: 16px;
            color: #9ca3af;
            font-size: 15px;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 56px;
            border-radius: 18px;
            border: 1px solid rgba(17,24,39,0.08);
            background: rgba(255,255,255,0.9);
            padding-inline-start: 46px;
            padding-inline-end: 18px;
            font-size: 15px;
            color: #111827;
            outline: none;
            transition: .18s ease;
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .form-input:focus {
            border-color: #1f4fd6;
            box-shadow: 0 0 0 4px rgba(31,79,214,0.10);
        }

        .form-error {
            margin-top: 8px;
            color: #dc2626;
            font-size: 12px;
        }

        .auth-row {
            position: relative;
            z-index: 1;
            margin: 10px 0 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            font-size: 13px;
        }

        .checkbox-inline {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
        }

        .checkbox-inline input {
            accent-color: #1f4fd6;
        }

        .auth-link {
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 700;
        }

        .btn-primary {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 56px;
            border: none;
            border-radius: 18px;
            background: linear-gradient(135deg, #1f4fd6 0%, #112f7c 100%);
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: .2s ease;
            box-shadow: 0 18px 34px rgba(17,47,124,0.20);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
        }

        .auth-micro {
            position: relative;
            z-index: 1;
            margin-top: 16px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .auth-chip {
            height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(31,79,214,0.08);
            color: #1d4ed8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        .auth-divider {
            position: relative;
            z-index: 1;
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid rgba(17,24,39,0.06);
        }

        .auth-footer {
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }

        .auth-footer a {
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 700;
        }

        .status-alert {
            position: relative;
            z-index: 1;
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(5,150,105,0.10);
            color: #059669;
            font-size: 14px;
        }

        .auth-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 1100px) {
            .ghost-layer {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .auth-page {
                padding: 12px;
            }

            .auth-card {
                width: 100%;
                border-radius: 24px;
                padding: 24px 18px;
            }

            .auth-card-title {
                font-size: 30px;
            }

            .auth-grid-2 {
                grid-template-columns: 1fr;
            }

            .auth-switch {
                width: 100%;
            }

            .auth-switch a {
                min-width: auto;
            }

            .topbar-actions {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @php
        $isArabic = app()->getLocale() === 'ar';
        $switchLocale = $isArabic ? 'en' : 'ar';
    @endphp

    <div class="preview-shell">
        <div class="preview-frame" id="previewFrame" data-device="desktop">
            <div class="auth-page">
                <div class="auth-topbar">
                    <a href="{{ route('welcome') }}" class="brand">
                        <div class="brand-mark">M</div>
                        <div>
                            <h1 class="brand-name">Mi'mar</h1>
                            <p class="brand-sub">{{ __('auth.platform_subtitle') }}</p>
                        </div>
                    </a>

                    <div class="topbar-actions">
                        <a href="{{ route('welcome') }}" class="topbar-btn">
                            {{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home' }}
                        </a>
                        <a href="{{ route('lang.switch', $switchLocale) }}" class="topbar-btn">
                            {{ __('auth.language_switch') }}
                        </a>

                        @if(request()->routeIs('login'))
                            <a href="{{ route('register') }}" class="topbar-btn">
                                {{ app()->getLocale() === 'ar' ? 'إنشاء حساب' : 'Register' }}
                            </a>
                        @elseif(request()->routeIs('register'))
                            <a href="{{ route('login') }}" class="topbar-btn">
                                {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Login' }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="auth-stage">
                    <div class="ghost-layer" aria-hidden="true">
                        <div class="ghost-panel one">
                            <div class="ghost-header"></div>
                            <div class="ghost-body">
                                <div class="ghost-line short"></div>
                                <div class="ghost-line long"></div>
                                <div class="ghost-line mid"></div>
                            </div>
                        </div>

                        <div class="ghost-panel two">
                            <div class="ghost-header"></div>
                            <div class="ghost-body">
                                <div class="ghost-line mid"></div>
                                <div class="ghost-line long"></div>
                                <div class="ghost-line short"></div>
                            </div>
                        </div>

                        <div class="ghost-panel three">
                            <div class="ghost-header"></div>
                            <div class="ghost-body">
                                <div class="ghost-line long"></div>
                                <div class="ghost-line short"></div>
                                <div class="ghost-line mid"></div>
                            </div>
                        </div>
                    </div>

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const frame = document.getElementById('previewFrame');
            const saved = localStorage.getItem('mimar-preview-device');
            if (frame && saved) {
                frame.setAttribute('data-device', saved);
            }
        })();
    </script>
</body>
</html>