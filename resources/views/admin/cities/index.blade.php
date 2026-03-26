@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $selectedCity = $selectedCity ?? null;

        $allCities = $cities->total();
        $activeCities = $cities->getCollection()->where('is_active', true)->count();
        $inactiveCities = $cities->getCollection()->where('is_active', false)->count();
        $selectedBusinessesCount = $selectedCity ? $selectedCity->businessAccounts->count() : 0;
    @endphp

    <style>
        .city-admin-shell {
            display: grid;
            gap: 24px;
        }

        .city-admin-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 34px;
            background:
                linear-gradient(135deg, #0f172a 0%, #172554 48%, #1e3a8a 100%);
            color: #fff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
        }

        .city-admin-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,.35), rgba(0,0,0,.92));
            pointer-events: none;
        }

        .city-admin-hero::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            top: -110px;
            inset-inline-end: -80px;
            background: radial-gradient(circle, rgba(255,255,255,.16), transparent 66%);
        }

        .city-admin-hero-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 24px;
            align-items: end;
        }

        .city-admin-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.14);
            margin-bottom: 16px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .02em;
        }

        .city-admin-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #eab308;
        }

        .city-admin-title {
            margin: 0 0 12px;
            font-size: 46px;
            line-height: 1.02;
            letter-spacing: -0.05em;
            font-weight: 900;
        }

        .city-admin-copy {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .city-admin-hero-side {
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 28px;
            padding: 20px;
            backdrop-filter: blur(12px);
        }

        .city-admin-hero-side h3 {
            margin: 0 0 14px;
            font-size: 19px;
            font-weight: 800;
        }

        .city-admin-hero-list {
            display: grid;
            gap: 12px;
        }

        .city-admin-hero-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,.86);
            font-size: 14px;
        }

        .city-admin-hero-item strong {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
        }

        .city-admin-alert {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(5,150,105,.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,.12);
            font-size: 14px;
            font-weight: 700;
        }

        .city-admin-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .city-stat-card {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 26px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
        }

        .city-stat-label {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .city-stat-number {
            font-size: 36px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
            margin-bottom: 6px;
        }

        .city-stat-note {
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.8;
        }

        .city-admin-layout {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 20px;
            align-items: start;
        }

        .city-panel,
        .city-detail-panel,
        .city-form-panel {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 30px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
            padding: 24px;
        }

        .city-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .city-panel-title {
            margin: 0;
            font-size: 25px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.03em;
        }

        .city-panel-sub {
            color: #64748b;
            font-size: 13px;
        }

        .city-cards-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .city-card {
            position: relative;
            text-decoration: none;
            display: block;
            padding: 18px;
            border-radius: 24px;
            background:
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
            transition: .22s ease;
            overflow: hidden;
        }

        .city-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 34px rgba(15,23,42,.08);
        }

        .city-card.active {
            border-color: rgba(59,130,246,.28);
            box-shadow: 0 18px 34px rgba(59,130,246,.10);
            background: linear-gradient(180deg, rgba(239,246,255,1) 0%, rgba(248,250,252,1) 100%);
        }

        .city-card.active::after {
            content: "";
            position: absolute;
            inset-inline-end: -20px;
            top: -20px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,.14), transparent 70%);
        }

        .city-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .city-card-name {
            margin: 0 0 4px;
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.15;
        }

        .city-card-sub {
            color: #64748b;
            font-size: 13px;
        }

        .city-badge {
            height: 30px;
            padding: 0 12px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .city-badge.active {
            background: rgba(5,150,105,.10);
            color: #059669;
        }

        .city-badge.inactive {
            background: rgba(239,68,68,.10);
            color: #dc2626;
        }

        .city-card-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .city-mini-box {
            padding: 12px;
            border-radius: 16px;
            background: rgba(255,255,255,.90);
            border: 1px solid rgba(15,23,42,.06);
        }

        .city-mini-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
        }

        .city-mini-box strong {
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
        }

        .city-pagination {
            margin-top: 18px;
        }

        .city-detail-title {
            margin: 0 0 6px;
            font-size: 32px;
            line-height: 1.05;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
        }

        .city-detail-sub {
            color: #64748b;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .city-detail-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .city-detail-box {
            padding: 14px;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
        }

        .city-detail-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .city-detail-box strong {
            display: block;
            color: #0f172a;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.7;
        }

        .city-business-wrap {
            margin-top: 20px;
        }

        .city-business-wrap h3 {
            margin: 0 0 12px;
            font-size: 19px;
            font-weight: 900;
            color: #0f172a;
        }

        .city-business-list {
            display: grid;
            gap: 12px;
        }

        .city-business-item {
            padding: 16px;
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
        }

        .city-business-item strong {
            display: block;
            margin-bottom: 6px;
            color: #0f172a;
            font-size: 16px;
            font-weight: 900;
        }

        .city-business-meta {
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }

        .city-business-status {
            margin-top: 10px;
            height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
        }

        .city-business-status.approved {
            background: rgba(5,150,105,.10);
            color: #059669;
        }

        .city-business-status.other {
            background: rgba(245,158,11,.12);
            color: #d97706;
        }

        .city-form {
            display: grid;
            gap: 16px;
        }

        .city-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .city-group {
            display: grid;
            gap: 8px;
        }

        .city-label {
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .city-input,
        .city-select {
            width: 100%;
            border: 1px solid rgba(15,23,42,.08);
            background: #fff;
            border-radius: 18px;
            padding: 13px 14px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .city-input:focus,
        .city-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.10);
        }

        .city-error {
            color: #dc2626;
            font-size: 12px;
        }

        .city-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .city-btn-primary,
        .city-btn-secondary,
        .city-btn-danger {
            height: 44px;
            padding: 0 16px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
        }

        .city-btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 14px 28px rgba(37,99,235,.18);
        }

        .city-btn-secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,.08);
        }

        .city-btn-danger {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.12);
        }

        .city-empty {
            padding: 30px;
            border-radius: 22px;
            background: #f8fafc;
            border: 1px dashed rgba(15,23,42,.10);
            color: #64748b;
            font-size: 14px;
            line-height: 1.9;
            text-align: center;
        }

        @media (max-width: 1200px) {
            .city-admin-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .city-admin-hero-content,
            .city-admin-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .city-cards-grid,
            .city-detail-grid,
            .city-form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .city-admin-hero,
            .city-panel,
            .city-detail-panel,
            .city-form-panel {
                padding: 20px;
                border-radius: 24px;
            }

            .city-admin-title {
                font-size: 32px;
            }
        }
    </style>

    <div class="city-admin-shell">
        <section class="city-admin-hero">
            <div class="city-admin-hero-content">
                <div>
                    <span class="city-admin-kicker">{{ $isArabic ? 'إدارة المحافظات والمدن' : 'Manage Governorates & Cities' }}</span>

                    <h1 class="city-admin-title">
                        {{ $isArabic ? 'أفخم واجهة لإدارة المدن والمحافظات داخل المنصة' : 'A premium experience for managing cities and governorates' }}
                    </h1>

                    <p class="city-admin-copy">
                        {{ $isArabic
                            ? 'واجهة إدارية أنيقة تتيح لك استعراض المحافظات، تعديلها، وإدارة حسابات الأعمال المرتبطة بها ضمن تجربة أوضح وأسهل وأكثر احترافية.'
                            : 'An elegant admin interface to review, update, and manage governorates and their linked business accounts in a clearer and more premium way.' }}
                    </p>
                </div>

                <div class="city-admin-hero-side">
                    <h3>{{ $isArabic ? 'ملخص مباشر' : 'Direct summary' }}</h3>

                    <div class="city-admin-hero-list">
                        <div class="city-admin-hero-item">
                            <span>{{ $isArabic ? 'إجمالي العناصر' : 'Total items' }}</span>
                            <strong>{{ $cities->total() }}</strong>
                        </div>

                        <div class="city-admin-hero-item">
                            <span>{{ $isArabic ? 'المعروضة الآن' : 'Shown now' }}</span>
                            <strong>{{ $cities->count() }}</strong>
                        </div>

                        <div class="city-admin-hero-item">
                            <span>{{ $isArabic ? 'الصفحة الحالية' : 'Current page' }}</span>
                            <strong>{{ $cities->currentPage() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="city-admin-alert">{{ session('success') }}</div>
        @endif

        <section class="city-admin-stats">
            <div class="city-stat-card">
                <span class="city-stat-label">{{ $isArabic ? 'إجمالي المحافظات/المدن' : 'Total cities/governorates' }}</span>
                <div class="city-stat-number">{{ $allCities }}</div>
                <div class="city-stat-note">{{ $isArabic ? 'كل العناصر المسجلة في النظام' : 'All entries recorded in the system' }}</div>
            </div>

            <div class="city-stat-card">
                <span class="city-stat-label">{{ $isArabic ? 'العناصر المفعلة' : 'Active items' }}</span>
                <div class="city-stat-number">{{ $activeCities }}</div>
                <div class="city-stat-note">{{ $isArabic ? 'المحافظات المتاحة داخل النظام' : 'Available entries in the system' }}</div>
            </div>

            <div class="city-stat-card">
                <span class="city-stat-label">{{ $isArabic ? 'العناصر غير المفعلة' : 'Inactive items' }}</span>
                <div class="city-stat-number">{{ $inactiveCities }}</div>
                <div class="city-stat-note">{{ $isArabic ? 'العناصر المخفية أو الموقوفة' : 'Hidden or disabled entries' }}</div>
            </div>

            <div class="city-stat-card">
                <span class="city-stat-label">{{ $isArabic ? 'أعمال العنصر المختار' : 'Selected item businesses' }}</span>
                <div class="city-stat-number">{{ $selectedBusinessesCount }}</div>
                <div class="city-stat-note">{{ $isArabic ? 'عدد حسابات الأعمال المرتبطة' : 'Linked business accounts count' }}</div>
            </div>
        </section>

        <section class="city-admin-layout">
            <div class="city-panel">
                <div class="city-panel-head">
                    <h2 class="city-panel-title">{{ $isArabic ? 'المحافظات والمدن' : 'Cities & Governorates' }}</h2>
                    <span class="city-panel-sub">{{ $cities->total() }}</span>
                </div>

                @if ($cities->count())
                    <div class="city-cards-grid">
                        @foreach ($cities as $city)
                            <a href="{{ route('admin.cities.index', ['selected' => $city->id]) }}" class="city-card {{ $selectedCity && $selectedCity->id === $city->id ? 'active' : '' }}">
                                <div class="city-card-top">
                                    <div>
                                        <h3 class="city-card-name">{{ $isArabic ? $city->name_ar : $city->name_en }}</h3>
                                        <div class="city-card-sub">{{ $isArabic ? $city->name_en : $city->name_ar }}</div>
                                    </div>

                                    <span class="city-badge {{ $city->is_active ? 'active' : 'inactive' }}">
                                        {{ $city->is_active
                                            ? ($isArabic ? 'مفعلة' : 'Active')
                                            : ($isArabic ? 'غير مفعلة' : 'Inactive') }}
                                    </span>
                                </div>

                                <div class="city-card-grid">
                                    <div class="city-mini-box">
                                        <span>{{ $isArabic ? 'الترتيب' : 'Sort order' }}</span>
                                        <strong>{{ $city->sort_order }}</strong>
                                    </div>

                                    <div class="city-mini-box">
                                        <span>{{ $isArabic ? 'حسابات الأعمال' : 'Business accounts' }}</span>
                                        <strong>{{ $city->business_accounts_count ?? 0 }}</strong>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="city-pagination">
                        {{ $cities->withQueryString()->links() }}
                    </div>
                @else
                    <div class="city-empty">
                        {{ $isArabic ? 'لا توجد مدن أو محافظات حالياً.' : 'There are no cities or governorates yet.' }}
                    </div>
                @endif
            </div>

            <div style="display:grid; gap:20px;">
                <div class="city-detail-panel">
                    <div class="city-panel-head">
                        <h2 class="city-panel-title">{{ $isArabic ? 'تفاصيل العنصر المختار' : 'Selected item details' }}</h2>
                    </div>

                    @if ($selectedCity)
                        <h3 class="city-detail-title">{{ $isArabic ? $selectedCity->name_ar : $selectedCity->name_en }}</h3>
                        <div class="city-detail-sub">{{ $isArabic ? $selectedCity->name_en : $selectedCity->name_ar }}</div>

                        <div class="city-detail-grid">
                            <div class="city-detail-box">
                                <span>{{ $isArabic ? 'الترتيب' : 'Sort order' }}</span>
                                <strong>{{ $selectedCity->sort_order }}</strong>
                            </div>

                            <div class="city-detail-box">
                                <span>{{ $isArabic ? 'الحالة' : 'Status' }}</span>
                                <strong>
                                    {{ $selectedCity->is_active
                                        ? ($isArabic ? 'مفعلة' : 'Active')
                                        : ($isArabic ? 'غير مفعلة' : 'Inactive') }}
                                </strong>
                            </div>

                            <div class="city-detail-box">
                                <span>{{ $isArabic ? 'عدد الأعمال' : 'Business count' }}</span>
                                <strong>{{ $selectedCity->businessAccounts->count() }}</strong>
                            </div>
                        </div>

                        <div class="city-business-wrap">
                            <h3>{{ $isArabic ? 'حسابات الأعمال المرتبطة' : 'Linked business accounts' }}</h3>

                            @if ($selectedCity->businessAccounts->count())
                                <div class="city-business-list">
                                    @foreach ($selectedCity->businessAccounts as $businessAccount)
                                        <div class="city-business-item">
                                            <strong>
                                                {{ $isArabic
                                                    ? ($businessAccount->name_ar ?? $businessAccount->name_en ?? '—')
                                                    : ($businessAccount->name_en ?? $businessAccount->name_ar ?? '—') }}
                                            </strong>

                                            <div class="city-business-meta">
                                                {{ $isArabic ? 'المالك:' : 'Owner:' }}
                                                {{ $businessAccount->user->name ?? '—' }}
                                            </div>

                                            <div class="city-business-meta">
                                                {{ $isArabic ? 'نوع النشاط:' : 'Activity type:' }}
                                                {{ $isArabic
                                                    ? ($businessAccount->activityType->name_ar ?? $businessAccount->activityType->name_en ?? '—')
                                                    : ($businessAccount->activityType->name_en ?? $businessAccount->activityType->name_ar ?? '—') }}
                                            </div>

                                            <span class="city-business-status {{ $businessAccount->status === 'approved' ? 'approved' : 'other' }}">
                                                {{ $businessAccount->status === 'approved'
                                                    ? ($isArabic ? 'مقبول' : 'Approved')
                                                    : ($isArabic ? 'قيد المراجعة/مرفوض' : 'Pending/Rejected') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="city-empty" style="margin-top:12px;">
                                    {{ $isArabic ? 'لا توجد حسابات أعمال مرتبطة بهذه المدينة حالياً.' : 'There are no business accounts linked to this city yet.' }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="city-empty">
                            {{ $isArabic ? 'اختر مدينة أو محافظة لعرض التفاصيل.' : 'Select a city or governorate to view details.' }}
                        </div>
                    @endif
                </div>

                <div class="city-form-panel">
                    <div class="city-panel-head">
                        <h2 class="city-panel-title">
                            {{ $selectedCity
                                ? ($isArabic ? 'تعديل المدينة/المحافظة' : 'Edit city/governorate')
                                : ($isArabic ? 'إضافة مدينة/محافظة' : 'Create city/governorate') }}
                        </h2>
                    </div>

                    <form
                        method="POST"
                        action="{{ $selectedCity ? route('admin.cities.update', $selectedCity->id) : route('admin.cities.store') }}"
                        class="city-form"
                    >
                        @csrf
                        @if ($selectedCity)
                            @method('PUT')
                        @endif

                        <div class="city-form-grid">
                            <div class="city-group">
                                <label class="city-label">{{ $isArabic ? 'الاسم بالعربية' : 'Arabic name' }}</label>
                                <input type="text" name="name_ar" class="city-input" value="{{ old('name_ar', $selectedCity->name_ar ?? '') }}">
                                @error('name_ar') <div class="city-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="city-group">
                                <label class="city-label">{{ $isArabic ? 'الاسم بالإنجليزية' : 'English name' }}</label>
                                <input type="text" name="name_en" class="city-input" value="{{ old('name_en', $selectedCity->name_en ?? '') }}">
                                @error('name_en') <div class="city-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="city-group">
                                <label class="city-label">{{ $isArabic ? 'الترتيب' : 'Sort order' }}</label>
                                <input type="number" name="sort_order" class="city-input" value="{{ old('sort_order', $selectedCity->sort_order ?? 0) }}">
                                @error('sort_order') <div class="city-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="city-group">
                                <label class="city-label">{{ $isArabic ? 'الحالة' : 'Status' }}</label>
                                <select name="is_active" class="city-select">
                                    <option value="1" @selected(old('is_active', $selectedCity->is_active ?? true) == 1)>{{ $isArabic ? 'مفعلة' : 'Active' }}</option>
                                    <option value="0" @selected(old('is_active', $selectedCity->is_active ?? true) == 0)>{{ $isArabic ? 'غير مفعلة' : 'Inactive' }}</option>
                                </select>
                                @error('is_active') <div class="city-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="city-actions">
                            <button type="submit" class="city-btn-primary">
                                {{ $selectedCity
                                    ? ($isArabic ? 'حفظ التعديلات' : 'Save changes')
                                    : ($isArabic ? 'إضافة المدينة/المحافظة' : 'Create city/governorate') }}
                            </button>

                            @if ($selectedCity)
                                <a href="{{ route('admin.cities.index') }}" class="city-btn-secondary">
                                    {{ $isArabic ? 'عنصر جديد' : 'New item' }}
                                </a>
                            @endif
                        </div>
                    </form>

                    @if ($selectedCity)
                        <form method="POST" action="{{ route('admin.cities.destroy', $selectedCity->id) }}" style="margin-top:14px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="city-btn-danger">
                                {{ $isArabic ? 'حذف المدينة/المحافظة' : 'Delete city/governorate' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection