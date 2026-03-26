<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? "Mi'mar" }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --bg: #f5f7fb;
            --surface: #ffffff;
            --surface-soft: #f8fafc;
            --text: #0f172a;
            --muted: #64748b;
            --line: rgba(15,23,42,0.08);

            --primary: #4458db;
            --primary-dark: #243873;
            --primary-soft: rgba(68,88,219,0.08);

            --success: #059669;
            --warning: #d97706;
            --danger: #dc2626;

            --shadow-soft: 0 12px 30px rgba(15,23,42,0.05);
            --shadow-main: 0 18px 40px rgba(15,23,42,0.08);

            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 16px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "IBM Plex Sans Arabic", "Inter", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(68,88,219,0.06), transparent 20%),
                radial-gradient(circle at bottom right, rgba(15,23,42,0.04), transparent 24%),
                var(--bg);
            color: var(--text);
        }

        a, button, input {
            font-family: inherit;
        }

        .app-shell {
            min-height: 100vh;
        }

        .app-header {
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
            background: rgba(255,255,255,0.82);
            border-bottom: 1px solid rgba(15,23,42,0.06);
        }

        .app-header-inner {
            max-width: 1360px;
            margin: 0 auto;
            min-height: 78px;
            padding: 14px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .app-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
            flex-shrink: 0;
        }

        .app-brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 18px;
            font-weight: 800;
        }

        .app-brand-name {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #24304d;
        }

        .app-brand-sub {
            margin: 2px 0 0;
            color: var(--muted);
            font-size: 12px;
        }

        .app-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            flex: 1;
        }

        .app-nav a {
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
            transition: .18s ease;
            white-space: nowrap;
        }

        .app-nav a:hover,
        .app-nav a.active {
            background: var(--primary-soft);
            color: var(--primary-dark);
        }

        .app-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .header-icon-btn {
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid rgba(15,23,42,0.08);
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(15,23,42,0.04);
            transition: .18s ease;
        }

        .header-icon-btn:hover {
            background: #f8fafc;
        }

        .header-user {
            height: 42px;
            padding: 0 8px 0 14px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid rgba(15,23,42,0.08);
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 20px rgba(15,23,42,0.04);
        }

        .header-user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 13px;
            font-weight: 800;
            overflow: hidden;
            flex-shrink: 0;
        }

        .header-user-name {
            font-size: 13px;
            font-weight: 700;
            color: #24304d;
            max-width: 130px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .logout-btn {
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            border: none;
            background: #fff1f2;
            color: var(--danger);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: .18s ease;
        }

        .logout-btn:hover {
            background: #ffe4e6;
        }

        .app-content {
            max-width: 1360px;
            margin: 0 auto;
            padding: 24px 22px 34px;
        }

        .page-grid {
            display: grid;
            gap: 22px;
        }

        .hero-card {
            background: linear-gradient(135deg, #182848 0%, #243b73 100%);
            color: #fff;
            border-radius: 30px;
            padding: 28px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 24px 54px rgba(24,40,72,0.22);
        }

        .hero-card::before {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            top: -120px;
            inset-inline-end: -100px;
            background: radial-gradient(circle, rgba(255,255,255,0.14), transparent 65%);
        }

        .hero-card::after {
            content: "";
            position: absolute;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            bottom: -100px;
            inset-inline-start: -80px;
            background: radial-gradient(circle, rgba(255,255,255,0.10), transparent 65%);
        }

        .hero-grid {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 20px;
            align-items: center;
        }

        .hero-title {
            margin: 0 0 10px;
            font-size: 42px;
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .hero-text {
            margin: 0;
            color: rgba(255,255,255,0.85);
            font-size: 15px;
            line-height: 1.95;
            max-width: 680px;
        }

        .hero-actions {
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .hero-btn-primary,
        .hero-btn-secondary {
            height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
        }

        .hero-btn-primary {
            background: #fff;
            color: #243873;
        }

        .hero-btn-secondary {
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.16);
            color: white;
        }

        .hero-side-card {
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.14);
            backdrop-filter: blur(14px);
            border-radius: 24px;
            padding: 18px;
        }

        .hero-side-title {
            margin: 0 0 12px;
            font-size: 18px;
            font-weight: 800;
        }

        .hero-side-list {
            display: grid;
            gap: 10px;
        }

        .hero-side-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.86);
            font-size: 14px;
            line-height: 1.8;
        }

        .hero-side-item::before {
            content: "•";
            font-size: 20px;
            line-height: 1;
        }

        .section-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 26px;
            padding: 24px;
            box-shadow: var(--shadow-soft);
        }

        .section-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .section-title {
            margin: 0;
            font-size: 30px;
            line-height: 1.08;
            font-weight: 800;
            color: #24304d;
            letter-spacing: -0.03em;
        }

        .section-link {
            color: var(--primary-dark);
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .quick-card {
            background: #fff;
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15,23,42,0.04);
        }

        .quick-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: var(--primary-soft);
            color: var(--primary-dark);
            display: grid;
            place-items: center;
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 14px;
        }

        .quick-title {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 800;
            color: #24304d;
        }

        .quick-text {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.85;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .category-card {
            background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 22px;
            padding: 20px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 10px 24px rgba(15,23,42,0.04);
        }

        .category-icon {
            width: 50px;
            height: 50px;
            border-radius: 16px;
            background: rgba(68,88,219,0.08);
            color: #4458db;
            display: grid;
            place-items: center;
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 14px;
        }

        .category-title {
            margin: 0 0 6px;
            font-size: 18px;
            font-weight: 800;
            color: #24304d;
        }

        .category-text {
            margin: 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .service-card {
            background: #fff;
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(15,23,42,0.05);
        }

        .service-card img {
            width: 100%;
            height: 210px;
            object-fit: cover;
            display: block;
        }

        .service-body {
            padding: 18px;
        }

        .service-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(68,88,219,0.08);
            color: #4458db;
            font-size: 12px;
            font-weight: 700;
        }

        .service-title {
            margin: 0 0 8px;
            font-size: 20px;
            font-weight: 800;
            color: #1f2f4d;
        }

        .service-text {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.85;
        }

        .service-meta {
            margin-top: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .service-price {
            font-size: 16px;
            font-weight: 800;
            color: #243873;
        }

        .service-action {
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }

        .feature-band {
            background: linear-gradient(135deg, #182848 0%, #243b73 100%);
            color: white;
            border-radius: 28px;
            padding: 28px;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 20px;
            align-items: center;
            box-shadow: 0 24px 54px rgba(24,40,72,0.22);
        }

        .feature-band h3 {
            margin: 0 0 10px;
            font-size: 32px;
            line-height: 1.1;
            font-weight: 800;
        }

        .feature-band p {
            margin: 0;
            color: rgba(255,255,255,0.84);
            font-size: 15px;
            line-height: 1.9;
            max-width: 700px;
        }

        .feature-cta {
            justify-self: end;
            min-width: 180px;
            height: 48px;
            padding: 0 18px;
            border-radius: 999px;
            background: #fff;
            color: #243873;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
            white-space: nowrap;
        }

        .activity-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .activity-box {
            background: #fff;
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 22px;
            padding: 20px;
        }

        .activity-title {
            margin: 0 0 14px;
            font-size: 20px;
            font-weight: 800;
            color: #24304d;
        }

        .activity-list {
            display: grid;
            gap: 12px;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(15,23,42,0.06);
        }

        .activity-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .activity-main {
            display: grid;
            gap: 4px;
        }

        .activity-main strong {
            font-size: 14px;
            color: #24304d;
        }

        .activity-main span {
            font-size: 13px;
            color: #64748b;
            line-height: 1.7;
        }

        .activity-status {
            font-size: 12px;
            font-weight: 700;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(5,150,105,0.10);
            color: #059669;
            white-space: nowrap;
        }

        @media (max-width: 1200px) {
            .quick-actions,
            .categories-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .services-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1023px) {
            .app-header-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .app-nav,
            .app-header-actions {
                width: 100%;
            }

            .hero-grid,
            .feature-band,
            .activity-grid {
                grid-template-columns: 1fr;
            }

            .feature-cta {
                justify-self: start;
            }
        }

        @media (max-width: 767px) {
            .app-header-inner {
                padding: 14px 16px;
            }

            .app-nav {
                justify-content: flex-start;
            }

            .app-content {
                padding: 18px 16px 24px;
            }

            .hero-card,
            .section-card {
                padding: 20px;
                border-radius: 22px;
            }

            .hero-title {
                font-size: 30px;
            }

            .quick-actions,
            .categories-grid,
            .services-grid {
                grid-template-columns: 1fr;
            }

            .service-card img {
                height: 180px;
            }

            .section-title {
                font-size: 26px;
            }

            .header-user-name {
                max-width: 90px;
            }
        }
    </style>
</head>
<body>
    @php
        $isArabic = app()->getLocale() === 'ar';
        $user = auth()->user();
        $userName = $user?->name ?? ($isArabic ? 'المستخدم' : 'User');
        $userInitial = mb_strtoupper(mb_substr($userName, 0, 1));
    @endphp

    <div class="app-shell">
        <header class="app-header">
            <div class="app-header-inner">
                <a href="{{ route('home') }}" class="app-brand">
                    <div class="app-brand-mark">M</div>
                    <div>
                        <h1 class="app-brand-name">Mi'mar</h1>
                        <p class="app-brand-sub">
                            {{ $isArabic ? 'منصة الخدمات العقارية والتقدير الذكي' : 'Real Estate Services & Smart Estimation' }}
                        </p>
                    </div>
                </a>

                <nav class="app-nav">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                        {{ $isArabic ? 'الرئيسية' : 'Home' }}
                    </a>

                    <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'التصنيفات' : 'Categories' }}
                    </a>

                    <a href="{{ route('estimations.create') }}" class="{{ request()->routeIs('estimations.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'التقدير الذكي' : 'Estimations' }}
                    </a>

                    <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'الطلبات' : 'Orders' }}
                    </a>
                    <a href="{{ route('favorites.index') }}">
                       {{ app()->getLocale() === 'ar' ? 'المفضلة' : 'Favorites' }}
                     </a>

                    <a href="{{ route('chat.index') }}" class="{{ request()->routeIs('chat.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'المحادثات' : 'Chat' }}
                    </a>

                    <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                        {{ $isArabic ? 'حسابي' : 'Profile' }}
                    </a>
                </nav>

                <div class="app-header-actions">
                    <a href="{{ route('lang.switch', $isArabic ? 'en' : 'ar') }}" class="header-icon-btn">
                        {{ $isArabic ? 'EN' : 'AR' }}
                    </a>

                    @auth
                        <a href="{{ route('notifications.index') }}" class="header-icon-btn">🔔</a>

                        <div class="header-user">
                            <div class="header-user-avatar">{{ $userInitial }}</div>
                            <div class="header-user-name">{{ $userName }}</div>
                        </div>

                        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="logout-btn">
                                {{ $isArabic ? 'تسجيل الخروج' : 'Logout' }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="header-icon-btn">
                            {{ $isArabic ? 'دخول' : 'Login' }}
                        </a>
                        <a href="{{ route('register') }}" class="header-icon-btn">
                            {{ $isArabic ? 'إنشاء حساب' : 'Register' }}
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="app-content">
            @yield('content')
        </main>
    </div>
</body>

</html>