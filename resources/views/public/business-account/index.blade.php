@extends('layouts.app')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $latestBusinessAccount = $latestBusinessAccount ?? null;
        $selectedBusinessAccount = $selectedBusinessAccount ?? $latestBusinessAccount;
        $businessAccounts = $businessAccounts ?? collect();
        $cities = $cities ?? collect();
        $activityTypes = $activityTypes ?? collect();
        $showCreateForm = $showCreateForm ?? false;
        $showEditForm = $showEditForm ?? false;

        $statusLabel = function ($status) use ($isArabic) {
            return match ($status) {
                'approved' => $isArabic ? 'مقبول' : 'Approved',
                'rejected' => $isArabic ? 'مرفوض' : 'Rejected',
                default => $isArabic ? 'قيد المراجعة' : 'Pending review',
            };
        };

        $statusClass = function ($status) {
            return match ($status) {
                'approved' => 'ba-status-approved',
                'rejected' => 'ba-status-rejected',
                default => 'ba-status-pending',
            };
        };

        $resolveBusinessName = function ($business) use ($isArabic) {
            return $isArabic
                ? ($business->name_ar ?? $business->name_en ?? '—')
                : ($business->name_en ?? $business->name_ar ?? '—');
        };

        $resolveActivityName = function ($business) use ($isArabic) {
            if (! $business || ! $business->activityType) {
                return '—';
            }

            return $isArabic
                ? ($business->activityType->name_ar ?? $business->activityType->name_en ?? '—')
                : ($business->activityType->name_en ?? $business->activityType->name_ar ?? '—');
        };

        $resolveCityName = function ($business) use ($isArabic) {
            if (! $business || ! $business->city) {
                return '—';
            }

            return $isArabic
                ? ($business->city->name_ar ?? $business->city->name_en ?? '—')
                : ($business->city->name_en ?? $business->city->name_ar ?? '—');
        };

        $activeCount = $businessAccounts->where('status', 'approved')->count();
        $pendingCount = $businessAccounts->where('status', 'pending')->count();
        $rejectedCount = $businessAccounts->where('status', 'rejected')->count();

        $formBusiness = $showEditForm ? $selectedBusinessAccount : null;

        $initialLat = old('latitude', $formBusiness->latitude ?? 33.5138);
        $initialLng = old('longitude', $formBusiness->longitude ?? 36.2765);
    @endphp

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    />

    <style>
        .ba-shell {
            display: grid;
            gap: 24px;
        }

        .ba-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 34px;
            background:
                linear-gradient(135deg, #0f172a 0%, #172554 48%, #1e3a8a 100%);
            color: #fff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
        }

        .ba-hero::before {
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

        .ba-hero::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            top: -110px;
            inset-inline-end: -80px;
            background: radial-gradient(circle, rgba(255,255,255,.16), transparent 66%);
        }

        .ba-hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 24px;
            align-items: end;
        }

        .ba-kicker {
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
        }

        .ba-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #eab308;
        }

        .ba-hero h1 {
            margin: 0 0 12px;
            font-size: 44px;
            line-height: 1.02;
            letter-spacing: -0.05em;
            font-weight: 900;
        }

        .ba-hero p {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .ba-hero-side {
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 28px;
            padding: 20px;
            backdrop-filter: blur(12px);
        }

        .ba-hero-side h3 {
            margin: 0 0 14px;
            font-size: 19px;
            font-weight: 800;
        }

        .ba-hero-list {
            display: grid;
            gap: 12px;
        }

        .ba-hero-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,.86);
            font-size: 14px;
        }

        .ba-hero-item strong {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
        }

        .ba-alert-success,
        .ba-alert-error {
            padding: 14px 16px;
            border-radius: 18px;
            font-size: 14px;
            font-weight: 700;
        }

        .ba-alert-success {
            background: rgba(5,150,105,.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,.12);
        }

        .ba-alert-error {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.12);
        }

        .ba-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .ba-stat-card {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 26px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
        }

        .ba-stat-label {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .ba-stat-number {
            font-size: 36px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
            margin-bottom: 6px;
        }

        .ba-stat-note {
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.8;
        }

        .ba-layout {
            display: grid;
            grid-template-columns: 1.02fr .98fr;
            gap: 20px;
            align-items: start;
        }

        .ba-card,
        .ba-form-card,
        .ba-empty-card,
        .ba-detail-card {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 30px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
            padding: 24px;
        }

        .ba-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .ba-title {
            margin: 0;
            font-size: 28px;
            font-weight: 900;
            color: #24304d;
            line-height: 1.05;
            letter-spacing: -0.03em;
        }

        .ba-sub {
            margin: 0 0 18px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.9;
        }

        .ba-btn {
            height: 44px;
            padding: 0 16px;
            border-radius: 999px;
            background: linear-gradient(135deg,#4458db 0%,#243873 100%);
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
            border: none;
            cursor: pointer;
            box-shadow: 0 14px 28px rgba(68,88,219,.16);
        }

        .ba-secondary-btn {
            height: 44px;
            padding: 0 16px;
            border-radius: 999px;
            background: #f8fafc;
            color: #334155;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
            border: 1px solid rgba(15,23,42,.08);
            cursor: pointer;
        }

        .ba-list {
            display: grid;
            gap: 12px;
        }

        .ba-list-item {
            display: block;
            text-decoration: none;
            padding: 18px;
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
            transition: .22s ease;
        }

        .ba-list-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 34px rgba(15,23,42,.08);
        }

        .ba-list-item.active {
            border-color: rgba(59,130,246,.28);
            box-shadow: 0 18px 34px rgba(59,130,246,.10);
            background: linear-gradient(180deg, rgba(239,246,255,1) 0%, rgba(248,250,252,1) 100%);
        }

        .ba-list-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: start;
            margin-bottom: 10px;
        }

        .ba-list-main strong {
            display: block;
            color: #24304d;
            font-size: 17px;
            margin-bottom: 6px;
            font-weight: 900;
        }

        .ba-list-main span {
            display: block;
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }

        .ba-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .ba-status-approved {
            background: rgba(5,150,105,.10);
            color: #059669;
        }

        .ba-status-rejected {
            background: rgba(239,68,68,.10);
            color: #dc2626;
        }

        .ba-status-pending {
            background: rgba(245,158,11,.12);
            color: #d97706;
        }

        .ba-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .ba-meta-box {
            padding: 14px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,.06);
        }

        .ba-meta-box span {
            display: block;
            margin-bottom: 6px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .ba-meta-box strong {
            display: block;
            color: #24304d;
            font-size: 15px;
            font-weight: 800;
            line-height: 1.6;
        }

        .ba-detail-note,
        .ba-reject-box {
            margin-top: 18px;
            padding: 16px;
            border-radius: 22px;
            border: 1px solid rgba(15,23,42,.06);
        }

        .ba-detail-note {
            background: #fff9ee;
            color: #7c5a10;
        }

        .ba-reject-box {
            background: rgba(239,68,68,.06);
            color: #991b1b;
            border-color: rgba(239,68,68,.10);
        }

        .ba-reject-box strong {
            display: block;
            margin-bottom: 6px;
        }

        .ba-gallery-wrap {
            margin-top: 20px;
            display: grid;
            gap: 16px;
        }

        .ba-section-title {
            margin: 0;
            font-size: 21px;
            font-weight: 900;
            color: #24304d;
        }

        .ba-gallery {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .ba-gallery-card {
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 18px;
            overflow: hidden;
            background: #fff;
        }

        .ba-gallery-card img {
            width: 100%;
            height: 190px;
            object-fit: cover;
            display: block;
        }

        .ba-gallery-card .meta {
            padding: 10px 12px;
            color: #475569;
            font-size: 12px;
        }

        .ba-docs {
            display: grid;
            gap: 12px;
        }

        .ba-doc {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 18px;
            background: #fff;
        }

        .ba-doc strong {
            color: #24304d;
            font-size: 14px;
        }

        .ba-doc a {
            color: #4458db;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .ba-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .ba-group {
            display: grid;
            gap: 8px;
        }

        .ba-group.full {
            grid-column: 1 / -1;
        }

        .ba-label {
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .ba-input,
        .ba-select,
        .ba-textarea,
        .ba-file {
            width: 100%;
            border: 1px solid rgba(15,23,42,.08);
            background: #fff;
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .ba-textarea {
            min-height: 140px;
            resize: vertical;
        }

        .ba-file {
            border-style: dashed;
        }

        .ba-input:focus,
        .ba-select:focus,
        .ba-textarea:focus {
            border-color: #4458db;
            box-shadow: 0 0 0 4px rgba(68,88,219,.10);
        }

        .ba-help {
            color: #64748b;
            font-size: 12px;
            line-height: 1.8;
        }

        .ba-error {
            color: #dc2626;
            font-size: 12px;
        }

        .ba-form-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ba-empty-card {
            display: grid;
            gap: 18px;
        }

        .ba-empty-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .ba-empty-box {
            padding: 18px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,.06);
        }

        .ba-empty-box strong {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            color: #24304d;
        }

        .ba-empty-box span {
            color: #64748b;
            font-size: 13px;
            line-height: 1.9;
        }

        .ba-map-wrap {
            display: grid;
            gap: 10px;
        }

        .ba-map-box {
            width: 100%;
            height: 360px;
            border-radius: 22px;
            border: 1px solid rgba(15,23,42,.08);
            overflow: hidden;
            background: #e2e8f0;
        }

        .ba-coordinates-preview {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .ba-coordinate-chip {
            padding: 12px 14px;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,.06);
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .ba-open-map-link {
            color: #4458db;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
        }

        @media (max-width: 1200px) {
            .ba-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .ba-hero-grid,
            .ba-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .ba-meta-grid,
            .ba-form-grid,
            .ba-gallery,
            .ba-empty-grid,
            .ba-coordinates-preview {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .ba-hero,
            .ba-card,
            .ba-form-card,
            .ba-empty-card,
            .ba-detail-card {
                padding: 20px;
                border-radius: 24px;
            }

            .ba-hero h1 {
                font-size: 32px;
            }

            .ba-stats {
                grid-template-columns: 1fr;
            }

            .ba-map-box {
                height: 300px;
            }
        }
    </style>

    <div class="ba-shell">
        <section class="ba-hero">
            <div class="ba-hero-grid">
                <div>
                    <span class="ba-kicker">{{ $isArabic ? 'إدارة حساب الأعمال' : 'Business account management' }}</span>
                    <h1>
                        {{ $isArabic
                            ? 'قدّم طلب حساب أعمال وابدأ رحلتك المهنية داخل Mi\'mar'
                            : 'Apply for a business account and start your professional journey inside Mi\'mar' }}
                    </h1>
                    <p>
                        {{ $isArabic
                            ? 'من هذه الصفحة يمكنك إرسال طلب حساب أعمال، متابعة حالته، تعديل بياناته، وإدارة الملفات المرفقة ضمن واجهة أوضح وأكثر احترافية.'
                            : 'From this page, you can submit a business account request, track its status, edit its details, and manage attached files through a clearer premium interface.' }}
                    </p>
                </div>

                <div class="ba-hero-side">
                    <h3>{{ $isArabic ? 'ملخص سريع' : 'Quick summary' }}</h3>
                    <div class="ba-hero-list">
                        <div class="ba-hero-item">
                            <span>{{ $isArabic ? 'إجمالي الطلبات' : 'Total requests' }}</span>
                            <strong>{{ $businessAccounts->count() }}</strong>
                        </div>
                        <div class="ba-hero-item">
                            <span>{{ $isArabic ? 'المقبولة' : 'Approved' }}</span>
                            <strong>{{ $activeCount }}</strong>
                        </div>
                        <div class="ba-hero-item">
                            <span>{{ $isArabic ? 'قيد المراجعة' : 'Pending' }}</span>
                            <strong>{{ $pendingCount }}</strong>
                        </div>
                        <div class="ba-hero-item">
                            <span>{{ $isArabic ? 'المرفوضة' : 'Rejected' }}</span>
                            <strong>{{ $rejectedCount }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="ba-alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="ba-alert-error">
                {{ $isArabic ? 'يرجى مراجعة الحقول وإصلاح الأخطاء الظاهرة أدناه.' : 'Please review the fields and fix the errors shown below.' }}
            </div>
        @endif

        <section class="ba-stats">
            <div class="ba-stat-card">
                <span class="ba-stat-label">{{ $isArabic ? 'كل الطلبات' : 'All requests' }}</span>
                <div class="ba-stat-number">{{ $businessAccounts->count() }}</div>
                <div class="ba-stat-note">{{ $isArabic ? 'كل طلبات حساب الأعمال الخاصة بك' : 'All your business account requests' }}</div>
            </div>

            <div class="ba-stat-card">
                <span class="ba-stat-label">{{ $isArabic ? 'المقبولة' : 'Approved' }}</span>
                <div class="ba-stat-number">{{ $activeCount }}</div>
                <div class="ba-stat-note">{{ $isArabic ? 'طلبات تمت الموافقة عليها' : 'Requests approved by admins' }}</div>
            </div>

            <div class="ba-stat-card">
                <span class="ba-stat-label">{{ $isArabic ? 'قيد المراجعة' : 'Pending' }}</span>
                <div class="ba-stat-number">{{ $pendingCount }}</div>
                <div class="ba-stat-note">{{ $isArabic ? 'طلبات تنتظر قرار الإدارة' : 'Requests under admin review' }}</div>
            </div>

            <div class="ba-stat-card">
                <span class="ba-stat-label">{{ $isArabic ? 'المرفوضة' : 'Rejected' }}</span>
                <div class="ba-stat-number">{{ $rejectedCount }}</div>
                <div class="ba-stat-note">{{ $isArabic ? 'طلبات بحاجة لمراجعة أو إعادة تقديم' : 'Requests that may need review or resubmission' }}</div>
            </div>
        </section>

        @if (! $businessAccounts->count())
            <section class="ba-empty-card">
                <div class="ba-head" style="margin-bottom: 0;">
                    <h2 class="ba-title">{{ $isArabic ? 'لا يوجد طلب حساب أعمال حتى الآن' : 'No business account request yet' }}</h2>

                    <a href="{{ route('business-account.index', ['new' => 1]) }}" class="ba-btn">
                        {{ $isArabic ? 'قدّم طلب حساب أعمال' : 'Submit business account request' }}
                    </a>
                </div>

                <p class="ba-sub" style="margin:0;">
                    {{ $isArabic
                        ? 'أنت تستخدم المنصة كمستخدم عادي حاليًا. عندما تصبح جاهزًا لنشر الخدمات واستقبال الطلبات، أرسل طلب حساب أعمال.'
                        : 'You are currently using the platform as a regular user. When you are ready to publish services and receive requests, submit a business account request.' }}
                </p>

                <div class="ba-empty-grid">
                    <div class="ba-empty-box">
                        <strong>{{ $isArabic ? 'الخطوة 1' : 'Step 1' }}</strong>
                        <span>{{ $isArabic ? 'أدخل بيانات نشاطك التجاري الأساسية.' : 'Enter your core business information.' }}</span>
                    </div>

                    <div class="ba-empty-box">
                        <strong>{{ $isArabic ? 'الخطوة 2' : 'Step 2' }}</strong>
                        <span>{{ $isArabic ? 'حدّد موقعك على الخريطة وأرفق الصور والوثائق.' : 'Pick your location on the map and attach supporting files.' }}</span>
                    </div>

                    <div class="ba-empty-box">
                        <strong>{{ $isArabic ? 'الخطوة 3' : 'Step 3' }}</strong>
                        <span>{{ $isArabic ? 'انتظر مراجعة الإدارة ثم ابدأ بإضافة خدماتك.' : 'Wait for admin review, then start publishing your services.' }}</span>
                    </div>
                </div>
            </section>
        @else
            <section class="ba-layout">
                <div class="ba-card">
                    <div class="ba-head">
                        <h2 class="ba-title">{{ $isArabic ? 'طلبات حساب الأعمال' : 'Business account requests' }}</h2>

                        <a href="{{ route('business-account.index', ['new' => 1]) }}" class="ba-btn">
                            {{ $isArabic ? 'طلب جديد' : 'New request' }}
                        </a>
                    </div>

                    <p class="ba-sub">
                        {{ $isArabic ? 'اختر أي طلب لعرض تفاصيله أو تعديله.' : 'Select any request to view its details or edit it.' }}
                    </p>

                    <div class="ba-list">
                        @foreach ($businessAccounts as $account)
                            <a href="{{ route('business-account.index', ['selected' => $account->id]) }}"
                               class="ba-list-item {{ $selectedBusinessAccount && $selectedBusinessAccount->id === $account->id ? 'active' : '' }}">
                                <div class="ba-list-top">
                                    <div class="ba-list-main">
                                        <strong>{{ $resolveBusinessName($account) }}</strong>
                                        <span>{{ $resolveActivityName($account) }} — {{ $resolveCityName($account) }}</span>
                                    </div>

                                    <span class="ba-status {{ $statusClass($account->status) }}">
                                        {{ $statusLabel($account->status) }}
                                    </span>
                                </div>

                                <div class="ba-meta-grid">
                                    <div class="ba-meta-box">
                                        <span>{{ $isArabic ? 'رقم الرخصة' : 'License number' }}</span>
                                        <strong>{{ $account->license_number ?? '—' }}</strong>
                                    </div>

                                    <div class="ba-meta-box">
                                        <span>{{ $isArabic ? 'عدد الصور' : 'Images count' }}</span>
                                        <strong>{{ $account->images?->count() ?? 0 }}</strong>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div style="display:grid; gap:20px;">
                    <div class="ba-detail-card">
                        <div class="ba-head">
                            <h2 class="ba-title">{{ $isArabic ? 'تفاصيل الطلب المختار' : 'Selected request details' }}</h2>

                            @if ($selectedBusinessAccount)
                                <a href="{{ route('business-account.index', ['selected' => $selectedBusinessAccount->id, 'edit' => 1]) }}" class="ba-secondary-btn">
                                    {{ $isArabic ? 'تعديل الطلب' : 'Edit request' }}
                                </a>
                            @endif
                        </div>

                        @if ($selectedBusinessAccount)
                            <div class="ba-status {{ $statusClass($selectedBusinessAccount->status) }}" style="margin-bottom:18px;">
                                {{ $statusLabel($selectedBusinessAccount->status) }}
                            </div>

                            <div class="ba-meta-grid">
                                <div class="ba-meta-box">
                                    <span>{{ $isArabic ? 'اسم النشاط' : 'Business name' }}</span>
                                    <strong>{{ $resolveBusinessName($selectedBusinessAccount) }}</strong>
                                </div>

                                <div class="ba-meta-box">
                                    <span>{{ $isArabic ? 'نوع النشاط' : 'Business type' }}</span>
                                    <strong>{{ $resolveActivityName($selectedBusinessAccount) }}</strong>
                                </div>

                                <div class="ba-meta-box">
                                    <span>{{ $isArabic ? 'المدينة' : 'City' }}</span>
                                    <strong>{{ $resolveCityName($selectedBusinessAccount) }}</strong>
                                </div>

                                <div class="ba-meta-box">
                                    <span>{{ $isArabic ? 'رقم الرخصة' : 'License number' }}</span>
                                    <strong>{{ $selectedBusinessAccount->license_number ?? '—' }}</strong>
                                </div>

                                <div class="ba-meta-box">
                                    <span>{{ $isArabic ? 'الإحداثيات' : 'Coordinates' }}</span>
                                    <strong>{{ $selectedBusinessAccount->latitude ?? '—' }} / {{ $selectedBusinessAccount->longitude ?? '—' }}</strong>
                                </div>

                                <div class="ba-meta-box">
                                    <span>{{ $isArabic ? 'عدد الوثائق' : 'Documents count' }}</span>
                                    <strong>{{ $selectedBusinessAccount->documents?->count() ?? 0 }}</strong>
                                </div>

                                <div class="ba-meta-box" style="grid-column: 1 / -1;">
                                    <span>{{ $isArabic ? 'النشاطات' : 'Activities' }}</span>
                                    <strong>{{ $selectedBusinessAccount->activities ?? '—' }}</strong>
                                </div>

                                <div class="ba-meta-box" style="grid-column: 1 / -1;">
                                    <span>{{ $isArabic ? 'التفاصيل' : 'Details' }}</span>
                                    <strong>{{ $selectedBusinessAccount->details ?? '—' }}</strong>
                                </div>
                            </div>

                            @if ($selectedBusinessAccount->latitude && $selectedBusinessAccount->longitude)
                                <div class="ba-detail-note">
                                    <strong>{{ $isArabic ? 'موقع النشاط:' : 'Business location:' }}</strong><br>
                                    <a
                                        class="ba-open-map-link"
                                        href="https://www.google.com/maps?q={{ $selectedBusinessAccount->latitude }},{{ $selectedBusinessAccount->longitude }}"
                                        target="_blank"
                                    >
                                        {{ $isArabic ? 'فتح الموقع على الخريطة' : 'Open location on map' }}
                                    </a>
                                </div>
                            @endif

                            @if ($selectedBusinessAccount->status === 'rejected' && $selectedBusinessAccount->rejection_reason)
                                <div class="ba-reject-box">
                                    <strong>{{ $isArabic ? 'سبب الرفض' : 'Rejection reason' }}</strong>
                                    <div>{{ $selectedBusinessAccount->rejection_reason }}</div>
                                </div>
                            @else
                                <div class="ba-detail-note">
                                    {{ $isArabic
                                        ? 'يمكنك متابعة حالة الطلب من هذه الصفحة، وتعديل البيانات إذا احتجت إلى إعادة التقديم أو تحديث المعلومات.'
                                        : 'You can track the request status from this page and edit the details whenever you need to resubmit or update information.' }}
                                </div>
                            @endif

                            @if ($selectedBusinessAccount->images && $selectedBusinessAccount->images->count())
                                <div class="ba-gallery-wrap">
                                    <h3 class="ba-section-title">{{ $isArabic ? 'الصور' : 'Images' }}</h3>

                                    <div class="ba-gallery">
                                        @foreach ($selectedBusinessAccount->images as $image)
                                            <div class="ba-gallery-card">
                                                <img src="{{ asset('storage/' . ltrim($image->path, '/')) }}" alt="business image">
                                                <div class="meta">{{ $isArabic ? 'صورة مرفوعة' : 'Uploaded image' }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($selectedBusinessAccount->documents && $selectedBusinessAccount->documents->count())
                                <div class="ba-gallery-wrap">
                                    <h3 class="ba-section-title">{{ $isArabic ? 'الوثائق' : 'Documents' }}</h3>

                                    <div class="ba-docs">
                                        @foreach ($selectedBusinessAccount->documents as $document)
                                            <div class="ba-doc">
                                                <strong>{{ $document->file_name ?? ($isArabic ? 'وثيقة مرفوعة' : 'Uploaded document') }}</strong>
                                                <a href="{{ asset('storage/' . ltrim($document->file_path, '/')) }}" target="_blank">
                                                    {{ $isArabic ? 'فتح' : 'Open' }}
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="ba-empty-card" style="padding:0; box-shadow:none; border:none;">
                                <div class="ba-empty-box">
                                    <strong>{{ $isArabic ? 'لا يوجد طلب محدد' : 'No selected request' }}</strong>
                                    <span>{{ $isArabic ? 'اختر طلبًا من القائمة لعرض التفاصيل.' : 'Select a request from the list to view its details.' }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if ($showCreateForm || $showEditForm)
                        <div class="ba-form-card">
                            <h2 class="ba-title">
                                {{ $showEditForm
                                    ? ($isArabic ? 'تعديل طلب حساب الأعمال' : 'Edit business account request')
                                    : ($isArabic ? 'إرسال طلب حساب أعمال جديد' : 'Submit a new business account request') }}
                            </h2>

                            <p class="ba-sub">
                                {{ $showEditForm
                                    ? ($isArabic ? 'عدّل البيانات ثم احفظ التحديثات لإعادة إرسال الطلب للمراجعة.' : 'Update the information and save to resubmit the request for review.')
                                    : ($isArabic ? 'املأ المعلومات الأساسية وأرفق الصور والوثائق المطلوبة لإرسال الطلب للإدارة.' : 'Fill in the core information and attach the required files to submit your request to the admin team.') }}
                            </p>

                            <form method="POST"
                                  action="{{ $showEditForm && $formBusiness ? route('business-account.update', $formBusiness->id) : route('business-account.store') }}"
                                  enctype="multipart/form-data">
                                @csrf
                                @if ($showEditForm && $formBusiness)
                                    @method('PUT')
                                @endif

                                <div class="ba-form-grid">
                                    <div class="ba-group">
                                        <label class="ba-label">{{ $isArabic ? 'اسم النشاط بالعربية' : 'Business name (Arabic)' }}</label>
                                        <input class="ba-input" type="text" name="name_ar" value="{{ old('name_ar', $formBusiness->name_ar ?? '') }}">
                                        @error('name_ar') <div class="ba-error">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="ba-group">
                                        <label class="ba-label">{{ $isArabic ? 'اسم النشاط بالإنجليزية' : 'Business name (English)' }}</label>
                                        <input class="ba-input" type="text" name="name_en" value="{{ old('name_en', $formBusiness->name_en ?? '') }}">
                                        @error('name_en') <div class="ba-error">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="ba-group">
                                        <label class="ba-label">{{ $isArabic ? 'نوع النشاط' : 'Business activity type' }}</label>
                                        <select class="ba-select" name="business_activity_type_id">
                                            <option value="">{{ $isArabic ? 'اختر نوع النشاط' : 'Select activity type' }}</option>
                                            @foreach ($activityTypes as $activityType)
                                                <option value="{{ $activityType->id }}"
                                                    @selected(old('business_activity_type_id', $formBusiness->business_activity_type_id ?? null) == $activityType->id)>
                                                    {{ $isArabic
                                                        ? ($activityType->name_ar ?? $activityType->name_en ?? ('#' . $activityType->id))
                                                        : ($activityType->name_en ?? $activityType->name_ar ?? ('#' . $activityType->id)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('business_activity_type_id') <div class="ba-error">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="ba-group">
                                        <label class="ba-label">{{ $isArabic ? 'المدينة' : 'City' }}</label>
                                        <select class="ba-select" name="city_id" id="citySelect">
                                            <option value="">{{ $isArabic ? 'اختر المدينة' : 'Select city' }}</option>
                                            @foreach ($cities as $city)
                                                @php
                                                    $cityNameAr = $city->name_ar ?? '';
                                                    $cityNameEn = $city->name_en ?? '';

                                                    $cityLat = match (true) {
                                                        str_contains($cityNameAr, 'دمشق') || str_contains(strtolower($cityNameEn), 'damascus') => 33.5138,
                                                        str_contains($cityNameAr, 'ريف دمشق') || str_contains(strtolower($cityNameEn), 'rural damascus') => 33.5000,
                                                        str_contains($cityNameAr, 'حلب') || str_contains(strtolower($cityNameEn), 'aleppo') => 36.2021,
                                                        str_contains($cityNameAr, 'حمص') || str_contains(strtolower($cityNameEn), 'homs') => 34.7308,
                                                        str_contains($cityNameAr, 'حماة') || str_contains(strtolower($cityNameEn), 'hama') => 35.1318,
                                                        str_contains($cityNameAr, 'اللاذقية') || str_contains(strtolower($cityNameEn), 'latakia') => 35.5317,
                                                        str_contains($cityNameAr, 'طرطوس') || str_contains(strtolower($cityNameEn), 'tartus') => 34.8890,
                                                        str_contains($cityNameAr, 'إدلب') || str_contains(strtolower($cityNameEn), 'idlib') => 35.9306,
                                                        str_contains($cityNameAr, 'درعا') || str_contains(strtolower($cityNameEn), 'daraa') => 32.6189,
                                                        str_contains($cityNameAr, 'السويداء') || str_contains(strtolower($cityNameEn), 'suwayda') => 32.7090,
                                                        str_contains($cityNameAr, 'القنيطرة') || str_contains(strtolower($cityNameEn), 'quneitra') => 33.1258,
                                                        str_contains($cityNameAr, 'دير الزور') || str_contains(strtolower($cityNameEn), 'deir') => 35.3333,
                                                        str_contains($cityNameAr, 'الرقة') || str_contains(strtolower($cityNameEn), 'raqqa') => 35.9500,
                                                        str_contains($cityNameAr, 'الحسكة') || str_contains(strtolower($cityNameEn), 'hasaka') => 36.5000,
                                                        default => 33.5138,
                                                    };

                                                    $cityLng = match (true) {
                                                        str_contains($cityNameAr, 'دمشق') || str_contains(strtolower($cityNameEn), 'damascus') => 36.2765,
                                                        str_contains($cityNameAr, 'ريف دمشق') || str_contains(strtolower($cityNameEn), 'rural damascus') => 36.3000,
                                                        str_contains($cityNameAr, 'حلب') || str_contains(strtolower($cityNameEn), 'aleppo') => 37.1343,
                                                        str_contains($cityNameAr, 'حمص') || str_contains(strtolower($cityNameEn), 'homs') => 36.7090,
                                                        str_contains($cityNameAr, 'حماة') || str_contains(strtolower($cityNameEn), 'hama') => 36.7578,
                                                        str_contains($cityNameAr, 'اللاذقية') || str_contains(strtolower($cityNameEn), 'latakia') => 35.7900,
                                                        str_contains($cityNameAr, 'طرطوس') || str_contains(strtolower($cityNameEn), 'tartus') => 35.8866,
                                                        str_contains($cityNameAr, 'إدلب') || str_contains(strtolower($cityNameEn), 'idlib') => 36.6339,
                                                        str_contains($cityNameAr, 'درعا') || str_contains(strtolower($cityNameEn), 'daraa') => 36.1021,
                                                        str_contains($cityNameAr, 'السويداء') || str_contains(strtolower($cityNameEn), 'suwayda') => 36.5695,
                                                        str_contains($cityNameAr, 'القنيطرة') || str_contains(strtolower($cityNameEn), 'quneitra') => 35.8246,
                                                        str_contains($cityNameAr, 'دير الزور') || str_contains(strtolower($cityNameEn), 'deir') => 40.1500,
                                                        str_contains($cityNameAr, 'الرقة') || str_contains(strtolower($cityNameEn), 'raqqa') => 39.0167,
                                                        str_contains($cityNameAr, 'الحسكة') || str_contains(strtolower($cityNameEn), 'hasaka') => 40.7500,
                                                        default => 36.2765,
                                                    };
                                                @endphp

                                                <option
                                                    value="{{ $city->id }}"
                                                    data-lat="{{ $cityLat }}"
                                                    data-lng="{{ $cityLng }}"
                                                    @selected(old('city_id', $formBusiness->city_id ?? null) == $city->id)
                                                >
                                                    {{ $isArabic
                                                        ? ($city->name_ar ?? $city->name_en ?? ('#' . $city->id))
                                                        : ($city->name_en ?? $city->name_ar ?? ('#' . $city->id)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('city_id') <div class="ba-error">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="ba-group">
                                        <label class="ba-label">{{ $isArabic ? 'رقم الرخصة' : 'License number' }}</label>
                                        <input class="ba-input" type="text" name="license_number" value="{{ old('license_number', $formBusiness->license_number ?? '') }}">
                                        @error('license_number') <div class="ba-error">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="ba-group full">
                                        <label class="ba-label">{{ $isArabic ? 'حدد الموقع على الخريطة' : 'Pick location on map' }}</label>

                                        <div class="ba-map-wrap">
                                            <div id="businessAccountMap" class="ba-map-box"></div>

                                            <div class="ba-help">
                                                {{ $isArabic
                                                    ? 'اضغط على الخريطة لتحديد موقع النشاط، أو اسحب العلامة للمكان الصحيح.'
                                                    : 'Click on the map to select the business location, or drag the marker to the exact place.' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ba-group">
                                        <label class="ba-label">{{ $isArabic ? 'خط العرض' : 'Latitude' }}</label>
                                        <input id="latitudeInput" class="ba-input" type="text" name="latitude" value="{{ old('latitude', $formBusiness->latitude ?? '') }}" readonly>
                                        @error('latitude') <div class="ba-error">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="ba-group">
                                        <label class="ba-label">{{ $isArabic ? 'خط الطول' : 'Longitude' }}</label>
                                        <input id="longitudeInput" class="ba-input" type="text" name="longitude" value="{{ old('longitude', $formBusiness->longitude ?? '') }}" readonly>
                                        @error('longitude') <div class="ba-error">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="ba-group full">
                                        <div class="ba-coordinates-preview">
                                            <div class="ba-coordinate-chip">
                                                {{ $isArabic ? 'Latitude:' : 'Latitude:' }}
                                                <span id="latitudePreview">{{ old('latitude', $formBusiness->latitude ?? '—') }}</span>
                                            </div>

                                            <div class="ba-coordinate-chip">
                                                {{ $isArabic ? 'Longitude:' : 'Longitude:' }}
                                                <span id="longitudePreview">{{ old('longitude', $formBusiness->longitude ?? '—') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ba-group full">
                                        <label class="ba-label">{{ $isArabic ? 'النشاطات' : 'Activities' }}</label>
                                        <textarea class="ba-textarea" name="activities">{{ old('activities', $formBusiness->activities ?? '') }}</textarea>
                                        @error('activities') <div class="ba-error">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="ba-group full">
                                        <label class="ba-label">{{ $isArabic ? 'تفاصيل النشاط' : 'Business details' }}</label>
                                        <textarea class="ba-textarea" name="details">{{ old('details', $formBusiness->details ?? '') }}</textarea>
                                        @error('details') <div class="ba-error">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="ba-group full">
                                        <label class="ba-label">{{ $isArabic ? 'صور النشاط' : 'Business images' }}</label>
                                        <input class="ba-file" type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp">
                                        <div class="ba-help">
                                            {{ $isArabic ? 'يمكنك رفع صور إضافية للنشاط التجاري.' : 'You can upload additional business images.' }}
                                        </div>
                                        @error('images') <div class="ba-error">{{ $message }}</div> @enderror
                                        @error('images.*') <div class="ba-error">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="ba-group full">
                                        <label class="ba-label">{{ $isArabic ? 'الوثائق' : 'Documents' }}</label>
                                        <input class="ba-file" type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp">
                                        <div class="ba-help">
                                            {{ $isArabic ? 'يمكنك رفع وثائق داعمة مثل الرخصة أو ملفات إضافية.' : 'You can upload supporting files such as licenses or additional documents.' }}
                                        </div>
                                        @error('documents') <div class="ba-error">{{ $message }}</div> @enderror
                                        @error('documents.*') <div class="ba-error">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="ba-form-actions">
                                    <button type="submit" class="ba-btn">
                                        {{ $showEditForm
                                            ? ($isArabic ? 'حفظ التعديلات' : 'Save changes')
                                            : ($isArabic ? 'إرسال الطلب' : 'Submit request') }}
                                    </button>

                                    <a href="{{ route('business-account.index', $selectedBusinessAccount ? ['selected' => $selectedBusinessAccount->id] : []) }}" class="ba-secondary-btn">
                                        {{ $isArabic ? 'إلغاء' : 'Cancel' }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </section>
        @endif
    </div>

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
    ></script>

    
            @if ($showCreateForm || $showEditForm)
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const latInput = document.getElementById('latitudeInput');
                            const lngInput = document.getElementById('longitudeInput');
                            const latPreview = document.getElementById('latitudePreview');
                            const lngPreview = document.getElementById('longitudePreview');
                            const mapElement = document.getElementById('businessAccountMap');
                            const citySelect = document.getElementById('citySelect');

                            if (!mapElement || !latInput || !lngInput) {
                                return;
                            }

                            const fallbackLat = 33.5138;
                            const fallbackLng = 36.2765;

                            const oldLat = parseFloat(latInput.value);
                            const oldLng = parseFloat(lngInput.value);

                            const hasSavedLocation = !isNaN(oldLat) && !isNaN(oldLng);

                            const startLat = hasSavedLocation ? oldLat : fallbackLat;
                            const startLng = hasSavedLocation ? oldLng : fallbackLng;

                            const map = L.map('businessAccountMap').setView(
                                [startLat, startLng],
                                hasSavedLocation ? 13 : 7
                            );

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '&copy; OpenStreetMap contributors'
                            }).addTo(map);

                            let marker = L.marker([startLat, startLng], {
                                draggable: true
                            }).addTo(map);

                            function updateLocation(lat, lng, moveMap = false) {
                                const normalizedLat = parseFloat(lat).toFixed(7);
                                const normalizedLng = parseFloat(lng).toFixed(7);

                                latInput.value = normalizedLat;
                                lngInput.value = normalizedLng;

                                if (latPreview) latPreview.textContent = normalizedLat;
                                if (lngPreview) lngPreview.textContent = normalizedLng;

                                marker.setLatLng([parseFloat(normalizedLat), parseFloat(normalizedLng)]);

                                if (moveMap) {
                                    map.setView([parseFloat(normalizedLat), parseFloat(normalizedLng)], 13);
                                }
                            }

                            updateLocation(startLat, startLng, false);

                            map.on('click', function (e) {
                                const { lat, lng } = e.latlng;
                                updateLocation(lat, lng, false);
                            });

                            marker.on('dragend', function (e) {
                                const position = e.target.getLatLng();
                                updateLocation(position.lat, position.lng, false);
                            });

                            if (citySelect) {
                                citySelect.addEventListener('change', function () {
                                    const selectedOption = this.options[this.selectedIndex];

                                    if (!selectedOption) {
                                        return;
                                    }

                                    const cityLat = parseFloat(selectedOption.dataset.lat);
                                    const cityLng = parseFloat(selectedOption.dataset.lng);

                                    if (isNaN(cityLat) || isNaN(cityLng)) {
                                        return;
                                    }

                                    if (!latInput.value || !lngInput.value || !hasSavedLocation) {
                                        updateLocation(cityLat, cityLng, true);
                                    } else {
                                        map.setView([cityLat, cityLng], 11);
                                    }
                                });

                                if (!hasSavedLocation && citySelect.value) {
                                    const selectedOption = citySelect.options[citySelect.selectedIndex];

                                    if (selectedOption) {
                                        const cityLat = parseFloat(selectedOption.dataset.lat);
                                        const cityLng = parseFloat(selectedOption.dataset.lng);

                                        if (!isNaN(cityLat) && !isNaN(cityLng)) {
                                            updateLocation(cityLat, cityLng, true);
                                        }
                                    }
                                }
                            }

                            setTimeout(() => {
                                map.invalidateSize();
                            }, 300);
                          });
                     </script>
                @endif
@endsection