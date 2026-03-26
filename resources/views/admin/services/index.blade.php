@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $filteredItems = $filteredItems ?? collect();
        $selectedItem = $selectedItem ?? null;
        $status = $status ?? 'all';

        $pageItems = collect($items->items() ?? []);
        $allCount = $items->total() ?? $filteredItems->count();
        $pendingCount = $pageItems->where('status', 'pending')->count();
        $approvedCount = $pageItems->where('status', 'approved')->count();
        $rejectedCount = $pageItems->where('status', 'rejected')->count();

        function resolveServiceNameAdmin($item, $isArabic) {
            return $isArabic
                ? ($item->name_ar ?? $item->name_en ?? '—')
                : ($item->name_en ?? $item->name_ar ?? '—');
        }

        function resolveBusinessNameForServiceAdmin($item, $isArabic) {
            if (! $item || ! $item->businessAccount) {
                return $isArabic ? 'مزود غير معروف' : 'Unknown provider';
            }

            return $isArabic
                ? ($item->businessAccount->name_ar ?? $item->businessAccount->name_en ?? '—')
                : ($item->businessAccount->name_en ?? $item->businessAccount->name_ar ?? '—');
        }

        function resolveCategoryNameAdmin($item, $isArabic) {
            if ($item && $item->subcategory) {
                return $isArabic
                    ? ($item->subcategory->name_ar ?? $item->subcategory->name_en ?? '—')
                    : ($item->subcategory->name_en ?? $item->subcategory->name_ar ?? '—');
            }

            if ($item && $item->category) {
                return $isArabic
                    ? ($item->category->name_ar ?? $item->category->name_en ?? '—')
                    : ($item->category->name_en ?? $item->category->name_ar ?? '—');
            }

            return '—';
        }

        function serviceStatusLabelAdmin($statusValue, $isArabic) {
            return match ($statusValue) {
                'approved' => $isArabic ? 'مقبولة' : 'Approved',
                'rejected' => $isArabic ? 'مرفوضة' : 'Rejected',
                default => $isArabic ? 'قيد المراجعة' : 'Pending',
            };
        }

        function serviceStatusClassAdmin($statusValue) {
            return match ($statusValue) {
                'approved' => 'sv-status-approved',
                'rejected' => 'sv-status-rejected',
                default => 'sv-status-pending',
            };
        }

        function resolveServiceImageAdmin($item) {
            $image = $item?->primaryImage()?->path;

            if (! $image) {
                return 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80';
            }

            if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
                return $image;
            }

            return asset('storage/' . ltrim($image, '/'));
        }
    @endphp

    <style>
        .sv-admin-shell {
            display: grid;
            gap: 22px;
        }

        .sv-admin-hero {
            border-radius: 28px;
            padding: 28px;
            background: linear-gradient(135deg, #151d38 0%, #23346f 100%);
            color: white;
            box-shadow: 0 24px 54px rgba(24, 40, 72, 0.22);
        }

        .sv-admin-hero h1 {
            margin: 0 0 10px;
            font-size: 34px;
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .sv-admin-hero p {
            margin: 0;
            color: rgba(255,255,255,0.84);
            font-size: 14px;
            line-height: 1.9;
            max-width: 760px;
        }

        .sv-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .sv-stat-card {
            background: white;
            border-radius: 20px;
            padding: 18px;
            border: 1px solid rgba(15,23,42,0.06);
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
        }

        .sv-stat-card strong {
            display: block;
            font-size: 28px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 6px;
        }

        .sv-stat-card span {
            display: block;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
        }

        .sv-stat-card small {
            display: block;
            margin-top: 6px;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.7;
        }

        .sv-admin-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .sv-admin-tab {
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            background: white;
            border: 1px solid rgba(15,23,42,0.08);
            color: #475569;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
        }

        .sv-admin-tab.active {
            background: linear-gradient(135deg, #6D5DF6, #8d81ff);
            color: white;
            box-shadow: 0 14px 26px rgba(109,93,246,0.18);
            border-color: transparent;
        }

        .sv-alert-success {
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(5,150,105,0.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,0.12);
            font-size: 14px;
            font-weight: 700;
        }

        .sv-admin-layout {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 20px;
            align-items: start;
        }

        .sv-list-card,
        .sv-details-card {
            background: white;
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
            border: 1px solid rgba(15,23,42,0.06);
        }

        .sv-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .sv-card-head h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #111827;
        }

        .sv-card-head span {
            color: #6b7280;
            font-size: 13px;
        }

        .sv-service-list {
            display: grid;
            gap: 12px;
        }

        .sv-service-link {
            display: block;
            text-decoration: none;
            padding: 16px;
            border-radius: 18px;
            background: #fafbff;
            border: 1px solid rgba(15,23,42,0.06);
            transition: .18s ease;
        }

        .sv-service-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(15,23,42,0.05);
        }

        .sv-service-link.active {
            border-color: rgba(109,93,246,0.30);
            background: linear-gradient(180deg, rgba(109,93,246,0.07), rgba(109,93,246,0.03));
            box-shadow: 0 14px 28px rgba(109,93,246,0.10);
        }

        .sv-service-name {
            margin: 0 0 8px;
            font-size: 17px;
            font-weight: 800;
            color: #111827;
        }

        .sv-service-meta {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        .sv-status {
            margin-top: 10px;
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

        .sv-status-pending {
            background: rgba(245,158,11,0.12);
            color: #d97706;
        }

        .sv-status-approved {
            background: rgba(5,150,105,0.10);
            color: #059669;
        }

        .sv-status-rejected {
            background: rgba(239,68,68,0.10);
            color: #dc2626;
        }

        .sv-details-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .sv-details-title {
            margin: 0 0 6px;
            font-size: 28px;
            font-weight: 800;
            color: #111827;
        }

        .sv-details-sub {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        .sv-main-grid {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 18px;
            align-items: start;
        }

        .sv-main-image img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 20px;
            display: block;
            border: 1px solid rgba(15,23,42,0.06);
        }

        .sv-admin-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .sv-admin-box {
            padding: 14px;
            border-radius: 18px;
            background: #fafbff;
            border: 1px solid rgba(15,23,42,0.06);
        }

        .sv-admin-box span {
            display: block;
            margin-bottom: 5px;
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
        }

        .sv-admin-box strong {
            display: block;
            color: #111827;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.7;
        }

        .sv-admin-details,
        .sv-admin-reason {
            margin-top: 14px;
            padding: 14px 16px;
            border-radius: 18px;
            background: #fafbff;
            border: 1px solid rgba(15,23,42,0.06);
            color: #4b5563;
            font-size: 14px;
            line-height: 1.9;
        }

        .sv-gallery-wrap {
            margin-top: 16px;
        }

        .sv-gallery-title {
            margin: 0 0 10px;
            font-size: 16px;
            font-weight: 800;
            color: #111827;
        }

        .sv-gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .sv-gallery-grid img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 16px;
            border: 1px solid rgba(15,23,42,0.06);
            display: block;
            background: #f8fafc;
        }

        .sv-admin-actions {
            margin-top: 16px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .sv-btn-primary,
        .sv-btn-danger {
            height: 42px;
            padding: 0 16px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
        }

        .sv-btn-primary {
            background: linear-gradient(135deg, #6D5DF6, #8d81ff);
            color: white;
        }

        .sv-btn-danger {
            background: rgba(239,68,68,0.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,0.12);
        }

        .sv-reject-form {
            display: grid;
            gap: 8px;
            width: 100%;
            max-width: 420px;
        }

        .sv-reject-form textarea {
            width: 100%;
            min-height: 92px;
            border: 1px solid rgba(15,23,42,0.08);
            background: white;
            border-radius: 16px;
            padding: 12px 14px;
            font-size: 14px;
            color: #111827;
            resize: vertical;
            outline: none;
        }

        .sv-reject-form textarea:focus {
            border-color: #6D5DF6;
            box-shadow: 0 0 0 4px rgba(109,93,246,0.10);
        }

        .sv-empty {
            padding: 28px;
            border-radius: 20px;
            background: #fafbff;
            border: 1px dashed rgba(15,23,42,0.10);
            color: #6b7280;
            font-size: 14px;
            line-height: 1.9;
            text-align: center;
        }

        .sv-pagination {
            margin-top: 18px;
        }

        @media (max-width: 1100px) {
            .sv-stats-grid,
            .sv-admin-grid,
            .sv-gallery-grid {
                grid-template-columns: 1fr 1fr;
            }

            .sv-admin-layout,
            .sv-main-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .sv-stats-grid,
            .sv-admin-grid,
            .sv-gallery-grid {
                grid-template-columns: 1fr;
            }

            .sv-admin-hero,
            .sv-list-card,
            .sv-details-card {
                padding: 20px;
                border-radius: 20px;
            }

            .sv-admin-hero h1 {
                font-size: 28px;
            }

            .sv-details-title {
                font-size: 24px;
            }
        }
    </style>

    <div class="sv-admin-shell">
        <section class="sv-admin-hero">
            <h1>{{ $isArabic ? 'مراجعة الخدمات' : 'Review Services' }}</h1>
            <p>
                {{ $isArabic
                    ? 'يمكنك هنا مشاهدة الخدمات بشكل مختصر، ثم اختيار أي خدمة لعرض التفاصيل الكاملة وقبولها أو رفضها.'
                    : 'Here you can browse services in a compact list, then select one to view its full details and approve or reject it.' }}
            </p>
        </section>

        <section class="sv-stats-grid">
            <div class="sv-stat-card">
                <strong>{{ $allCount }}</strong>
                <span>{{ $isArabic ? 'إجمالي الخدمات' : 'Total Services' }}</span>
                <small>{{ $isArabic ? 'كل الخدمات الموجودة ضمن الصفحة الحالية.' : 'All services in the current page.' }}</small>
            </div>

            <div class="sv-stat-card">
                <strong>{{ $pendingCount }}</strong>
                <span>{{ $isArabic ? 'قيد المراجعة' : 'Pending' }}</span>
                <small>{{ $isArabic ? 'بانتظار مراجعة الإدارة.' : 'Waiting for admin review.' }}</small>
            </div>

            <div class="sv-stat-card">
                <strong>{{ $approvedCount }}</strong>
                <span>{{ $isArabic ? 'المقبولة' : 'Approved' }}</span>
                <small>{{ $isArabic ? 'الخدمات التي تم قبولها.' : 'Services already approved.' }}</small>
            </div>

            <div class="sv-stat-card">
                <strong>{{ $rejectedCount }}</strong>
                <span>{{ $isArabic ? 'المرفوضة' : 'Rejected' }}</span>
                <small>{{ $isArabic ? 'الخدمات التي تم رفضها.' : 'Services already rejected.' }}</small>
            </div>
        </section>

        <div class="sv-admin-tabs">
            <a href="{{ route('admin.services.index', ['status' => 'all']) }}" class="sv-admin-tab {{ $status === 'all' ? 'active' : '' }}">
                {{ $isArabic ? 'الكل' : 'All' }}
            </a>
            <a href="{{ route('admin.services.index', ['status' => 'pending']) }}" class="sv-admin-tab {{ $status === 'pending' ? 'active' : '' }}">
                {{ $isArabic ? 'قيد المراجعة' : 'Pending' }}
            </a>
            <a href="{{ route('admin.services.index', ['status' => 'approved']) }}" class="sv-admin-tab {{ $status === 'approved' ? 'active' : '' }}">
                {{ $isArabic ? 'المقبولة' : 'Approved' }}
            </a>
            <a href="{{ route('admin.services.index', ['status' => 'rejected']) }}" class="sv-admin-tab {{ $status === 'rejected' ? 'active' : '' }}">
                {{ $isArabic ? 'المرفوضة' : 'Rejected' }}
            </a>
        </div>

        @if (session('success'))
            <div class="sv-alert-success">
                {{ session('success') }}
            </div>
        @endif

        <section class="sv-admin-layout">
            <div class="sv-list-card">
                <div class="sv-card-head">
                    <h2>{{ $isArabic ? 'الخدمات الموجودة' : 'Available Services' }}</h2>
                    <span>{{ $filteredItems->count() }} {{ $isArabic ? 'خدمة' : 'services' }}</span>
                </div>

                @if ($filteredItems->count())
                    <div class="sv-service-list">
                        @foreach ($filteredItems as $item)
                            <a
                                href="{{ route('admin.services.index', ['status' => $status, 'selected' => $item->id]) }}"
                                class="sv-service-link {{ $selectedItem && $selectedItem->id === $item->id ? 'active' : '' }}"
                            >
                                <h3 class="sv-service-name">{{ resolveServiceNameAdmin($item, $isArabic) }}</h3>
                                <div class="sv-service-meta">
                                    {{ resolveBusinessNameForServiceAdmin($item, $isArabic) }}<br>
                                    {{ resolveCategoryNameAdmin($item, $isArabic) }}
                                </div>

                                <span class="sv-status {{ serviceStatusClassAdmin($item->status) }}">
                                    {{ serviceStatusLabelAdmin($item->status, $isArabic) }}
                                </span>
                            </a>
                        @endforeach
                    </div>

                    <div class="sv-pagination">
                        {{ $items->withQueryString()->links() }}
                    </div>
                @else
                    <div class="sv-empty">
                        {{ $isArabic ? 'لا توجد خدمات ضمن هذا الفلتر حاليًا.' : 'There are no services in this filter right now.' }}
                    </div>
                @endif
            </div>

            <div class="sv-details-card">
                @if ($selectedItem)
                    <div class="sv-details-head">
                        <div>
                            <h2 class="sv-details-title">{{ resolveServiceNameAdmin($selectedItem, $isArabic) }}</h2>
                            <div class="sv-details-sub">
                                {{ $isArabic ? 'مزود الخدمة:' : 'Provider:' }}
                                {{ resolveBusinessNameForServiceAdmin($selectedItem, $isArabic) }}
                            </div>
                        </div>

                        <span class="sv-status {{ serviceStatusClassAdmin($selectedItem->status) }}">
                            {{ serviceStatusLabelAdmin($selectedItem->status, $isArabic) }}
                        </span>
                    </div>

                    <div class="sv-main-grid">
                        <div class="sv-main-image">
                            <img src="{{ resolveServiceImageAdmin($selectedItem) }}" alt="service image">
                        </div>

                        <div>
                            <div class="sv-admin-grid">
                                <div class="sv-admin-box">
                                    <span>{{ $isArabic ? 'السعر' : 'Price' }}</span>
                                    <strong>{{ $selectedItem->price ?? '—' }}</strong>
                                </div>

                                <div class="sv-admin-box">
                                    <span>{{ $isArabic ? 'التصنيف' : 'Category' }}</span>
                                    <strong>{{ resolveCategoryNameAdmin($selectedItem, $isArabic) }}</strong>
                                </div>

                                <div class="sv-admin-box">
                                    <span>{{ $isArabic ? 'تاريخ الإنشاء' : 'Created At' }}</span>
                                    <strong>{{ optional($selectedItem->created_at)->format('Y-m-d') ?? '—' }}</strong>
                                </div>

                                <div class="sv-admin-box">
                                    <span>{{ $isArabic ? 'الحالة' : 'Status' }}</span>
                                    <strong>{{ serviceStatusLabelAdmin($selectedItem->status, $isArabic) }}</strong>
                                </div>
                            </div>

                            @if ($selectedItem->description)
                                <div class="sv-admin-details">
                                    <strong>{{ $isArabic ? 'الوصف:' : 'Description:' }}</strong><br>
                                    {{ $selectedItem->description }}
                                </div>
                            @endif

                            @if ($selectedItem->status === 'rejected' && $selectedItem->rejection_reason)
                                <div class="sv-admin-reason">
                                    <strong>{{ $isArabic ? 'سبب الرفض:' : 'Rejection reason:' }}</strong><br>
                                    {{ $selectedItem->rejection_reason }}
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($selectedItem->images && $selectedItem->images->count())
                        <div class="sv-gallery-wrap">
                            <h3 class="sv-gallery-title">{{ $isArabic ? 'صور الخدمة' : 'Service Images' }}</h3>
                            <div class="sv-gallery-grid">
                                @foreach ($selectedItem->images->take(6) as $image)
                                    <img src="{{ asset('storage/' . ltrim($image->path, '/')) }}" alt="service image">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($selectedItem->status === 'pending')
                        <div class="sv-admin-actions">
                            <form method="POST" action="{{ route('admin.services.approve', $selectedItem->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="{{ $status }}">
                                <button type="submit" class="sv-btn-primary">
                                    {{ $isArabic ? 'قبول الخدمة' : 'Approve Service' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.services.reject', $selectedItem->id) }}" class="sv-reject-form">
                                @csrf
                                <input type="hidden" name="status" value="{{ $status }}">
                                <textarea
                                    name="rejection_reason"
                                    placeholder="{{ $isArabic ? 'اكتب سبب الرفض...' : 'Write rejection reason...' }}"
                                    required
                                ></textarea>

                                <button type="submit" class="sv-btn-danger">
                                    {{ $isArabic ? 'رفض الخدمة' : 'Reject Service' }}
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="sv-empty">
                        {{ $isArabic ? 'اختر خدمة من القائمة لعرض التفاصيل.' : 'Select a service from the list to view details.' }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection