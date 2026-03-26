<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? "Mi'mar Admin" }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #6D5DF6;
            --primary-dark: #5548d9;
            --sidebar: #111827;
            --sidebar-soft: #1f2937;
            --bg-main: #f7f8fc;
            --white: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --line: rgba(17, 24, 39, 0.08);
            --shadow-soft: 0 14px 30px rgba(17, 24, 39, 0.06);
        }

        body {
            margin: 0;
            font-family: "Cairo", "Tajawal", sans-serif;
            background: var(--bg-main);
            color: var(--text-main);
        }

        * {
            box-sizing: border-box;
        }

        .admin-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        .admin-sidebar {
            background: linear-gradient(180deg, var(--sidebar), var(--sidebar-soft));
            color: white;
            padding: 26px 18px;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
            text-decoration: none;
            color: white;
        }

        .admin-brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, #6D5DF6, #8d81ff);
            display: grid;
            place-items: center;
            font-weight: 800;
        }

        .admin-brand-name {
            font-size: 22px;
            font-weight: 800;
        }

        .admin-brand-sub {
            font-size: 12px;
            color: rgba(255,255,255,0.72);
            margin-top: 2px;
        }

        .admin-nav {
            display: grid;
            gap: 8px;
        }

        .admin-nav a {
            color: rgba(255,255,255,0.82);
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 14px;
            font-weight: 700;
            transition: .18s ease;
        }

        .admin-nav a:hover,
        .admin-nav a.active {
            background: rgba(255,255,255,0.08);
            color: white;
        }

        .admin-sidebar-footer {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: grid;
            gap: 10px;
        }

        .admin-side-btn {
            height: 42px;
            padding: 0 14px;
            border-radius: 12px;
            border: none;
            background: rgba(255,255,255,0.08);
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            cursor: pointer;
        }

        .admin-main {
            padding: 24px;
        }

        .admin-topbar {
            min-height: 76px;
            background: white;
            border-radius: 22px;
            box-shadow: var(--shadow-soft);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 22px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .admin-topbar-title {
            font-weight: 800;
            font-size: 20px;
            color: var(--text-main);
        }

        .admin-topbar-subtitle {
            color: var(--text-muted);
            font-size: 14px;
        }

        .admin-topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .admin-topbar-btn {
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: white;
            color: var(--text-main);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
        }

        .admin-page-card {
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-soft);
            padding: 24px;
        }

        @media (max-width: 992px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                display: none;
            }

            .admin-main {
                padding: 14px;
            }
        }
    </style>
</head>
<body>
    @php
        $isArabic = app()->getLocale() === 'ar';
    @endphp

    <div class="admin-layout">
        <aside class="admin-sidebar">
            <a href="{{ route('dashboard') }}" class="admin-brand">
                <div class="admin-brand-logo">M</div>
                <div>
                    <div class="admin-brand-name">Mi'mar</div>
                    <div class="admin-brand-sub">
                        {{ $isArabic ? 'لوحة إدارة المنصة' : 'Platform Admin Panel' }}
                    </div>
                </div>
            </a>

            <nav class="admin-nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    {{ $isArabic ? 'لوحة التحكم' : 'Dashboard' }}
                </a>

                <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                 {{ $isArabic ? 'الخدمات' : 'Services' }}
               </a>

                <a href="{{ route('admin.business-accounts.index') }}" class="{{ request()->routeIs('admin.business-accounts.*') ? 'active' : '' }}">
                     {{ $isArabic ? 'حسابات الأعمال' : 'Business Accounts' }}
                 </a>

                <a href="{{ route('estimations.create') }}" class="{{ request()->routeIs('estimations.*') ? 'active' : '' }}">
                    {{ $isArabic ? 'التقدير الذكي' : 'Estimations' }}
                </a>

                <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                    {{ $isArabic ? 'الطلبات' : 'Orders' }}
                </a>
                <a href="{{ route('admin.cities.index') }}" class="{{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                 {{ $isArabic ? 'المدن' : 'Cities' }}
               </a>
                <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    {{ $isArabic ? 'الإشعارات' : 'Notifications' }}
                </a>
                <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                   {{ $isArabic ? 'البلاغات' : 'Reports' }}
               </a>
            </nav>

            <div class="admin-sidebar-footer">
                <a href="{{ route('lang.switch', $isArabic ? 'en' : 'ar') }}" class="admin-side-btn">
                    {{ $isArabic ? 'English' : 'العربية' }}
                </a>

                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="admin-side-btn" style="width:100%;">
                        {{ $isArabic ? 'تسجيل الخروج' : 'Logout' }}
                    </button>
                </form>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-topbar">
                <div>
                    <div class="admin-topbar-title">
                        {{ $title ?? ($isArabic ? 'لوحة الإدارة' : 'Admin Panel') }}
                    </div>
                    <div class="admin-topbar-subtitle">
                        {{ $isArabic ? 'نظام إدارة منصة Mi\'mar' : 'Mi\'mar platform management system' }}
                    </div>
                </div>

                <div class="admin-topbar-actions">
                    <a href="{{ route('dashboard') }}" class="admin-topbar-btn">
                        {{ $isArabic ? 'الرئيسية' : 'Home' }}
                    </a>

                    <a href="{{ route('lang.switch', $isArabic ? 'en' : 'ar') }}" class="admin-topbar-btn">
                        {{ $isArabic ? 'EN' : 'AR' }}
                    </a>
                </div>
            </div>

            @yield('content')
        </main>
    </div>
</body>
</html>