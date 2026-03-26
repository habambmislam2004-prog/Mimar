@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $filteredItems = $filteredItems ?? collect();
        $selectedItem = $selectedItem ?? null;
        $status = $status ?? 'all';

        $allCount = $items->total() ?? $filteredItems->count();
        $pendingCount = collect($items->items() ?? [])->where('status', 'pending')->count();
        $approvedCount = collect($items->items() ?? [])->where('status', 'approved')->count();
        $rejectedCount = collect($items->items() ?? [])->where('status', 'rejected')->count();

        function resolveBusinessNameAdmin($item, $isArabic) {
            return $isArabic
                ? ($item->name_ar ?? $item->name_en ?? '—')
                : ($item->name_en ?? $item->name_ar ?? '—');
        }

        function resolveActivityNameAdmin($item, $isArabic) {
            if (! $item || ! $item->activityType) {
                return '—';
            }

            return $isArabic
                ? ($item->activityType->name_ar ?? $item->activityType->name_en ?? '—')
                : ($item->activityType->name_en ?? $item->activityType->name_ar ?? '—');
        }

        function resolveCityNameAdmin($item, $isArabic) {
            if (! $item || ! $item->city) {
                return '—';
            }

            return $isArabic
                ? ($item->city->name_ar ?? $item->city->name_en ?? '—')
                : ($item->city->name_en ?? $item->city->name_ar ?? '—');
        }

        function businessStatusLabelAdmin($statusValue, $isArabic) {
            return match ($statusValue) {
                'approved' => $isArabic ? 'مقبول' : 'Approved',
                'rejected' => $isArabic ? 'مرفوض' : 'Rejected',
                default => $isArabic ? 'قيد المراجعة' : 'Pending',
            };
        }

        function businessStatusClassAdmin($statusValue) {
            return match ($statusValue) {
                'approved' => 'ba-status-approved',
                'rejected' => 'ba-status-rejected',
                default => 'ba-status-pending',
            };
        }
    @endphp

    <style>
        .ba-admin-shell {
            display: grid;
            gap: 22px;
        }

        .ba-admin-hero {
            border-radius: 28px;
            padding: 28px;
            background: linear-gradient(135deg, #151d38 0%, #23346f 100%);
            color: white;
            box-shadow: 0 24px 54px rgba(24, 40, 72, 0.22);
        }

        .ba-admin-hero h1 {
            margin: 0 0 10px;
            font-size: 34px;
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .ba-admin-hero p {
            margin: 0;
            color: rgba(255,255,255,0.84);
            font-size: 14px;
            line-height: 1.9;
            max-width: 760px;
        }

        .ba-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .ba-stat-card {
            background: white;
            border-radius: 20px;
            padding: 18px;
            border: 1px solid rgba(15,23,42,0.06);
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
        }

        .ba-stat-card strong {
            display: block;
            font-size: 28px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 6px;
        }

        .ba-stat-card span {
            display: block;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
        }

        .ba-stat-card small {
            display: block;
            margin-top: 6px;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.7;
        }

        .ba-admin-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ba-admin-tab {
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

        .ba-admin-tab.active {
            background: linear-gradient(135deg, #6D5DF6, #8d81ff);
            color: white;
            box-shadow: 0 14px 26px rgba(109,93,246,0.18);
            border-color: transparent;
        }

        .ba-alert-success {
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(5,150,105,0.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,0.12);
            font-size: 14px;
            font-weight: 700;
        }

        .ba-admin-layout {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 20px;
            align-items: start;
        }

        .ba-list-card,
        .ba-details-card {
            background: white;
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
            border: 1px solid rgba(15,23,42,0.06);
        }

        .ba-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .ba-card-head h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #111827;
        }

        .ba-card-head span {
            color: #6b7280;
            font-size: 13px;
        }

        .ba-account-list {
            display: grid;
            gap: 12px;
        }

        .ba-account-link {
            display: block;
            text-decoration: none;
            padding: 16px;
            border-radius: 18px;
            background: #fafbff;
            border: 1px solid rgba(15,23,42,0.06);
            transition: .18s ease;
        }

        .ba-account-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(15,23,42,0.05);
        }

        .ba-account-link.active {
            border-color: rgba(109,93,246,0.30);
            background: linear-gradient(180deg, rgba(109,93,246,0.07), rgba(109,93,246,0.03));
            box-shadow: 0 14px 28px rgba(109,93,246,0.10);
        }

        .ba-account-name {
            margin: 0 0 8px;
            font-size: 17px;
            font-weight: 800;
            color: #111827;
        }

        .ba-account-meta {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        .ba-status {
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

        .ba-status-pending {
            background: rgba(245,158,11,0.12);
            color: #d97706;
        }

        .ba-status-approved {
            background: rgba(5,150,105,0.10);
            color: #059669;
        }

        .ba-status-rejected {
            background: rgba(239,68,68,0.10);
            color: #dc2626;
        }

        .ba-details-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .ba-details-title {
            margin: 0 0 6px;
            font-size: 28px;
            font-weight: 800;
            color: #111827;
        }

        .ba-details-sub {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        .ba-admin-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .ba-admin-box {
            padding: 14px;
            border-radius: 18px;
            background: #fafbff;
            border: 1px solid rgba(15,23,42,0.06);
        }

        .ba-admin-box span {
            display: block;
            margin-bottom: 5px;
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
        }

        .ba-admin-box strong {
            display: block;
            color: #111827;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.7;
        }

        .ba-admin-details,
        .ba-admin-reason {
            margin-top: 14px;
            padding: 14px 16px;
            border-radius: 18px;
            background: #fafbff;
            border: 1px solid rgba(15,23,42,0.06);
            color: #4b5563;
            font-size: 14px;
            line-height: 1.9;
        }

        .ba-images-wrap {
            margin-top: 16px;
        }

        .ba-images-title {
            margin: 0 0 10px;
            font-size: 16px;
            font-weight: 800;
            color: #111827;
        }

        .ba-images-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .ba-images-grid img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 16px;
            border: 1px solid rgba(15,23,42,0.06);
            display: block;
            background: #f8fafc;
        }

        .ba-admin-actions {
            margin-top: 16px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .ba-btn-primary,
        .ba-btn-danger {
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

        .ba-btn-primary {
            background: linear-gradient(135deg, #6D5DF6, #8d81ff);
            color: white;
        }

        .ba-btn-danger {
            background: rgba(239,68,68,0.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,0.12);
        }

        .ba-reject-form {
            display: grid;
            gap: 8px;
            width: 100%;
            max-width: 420px;
        }

        .ba-reject-form textarea {
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

        .ba-reject-form textarea:focus {
            border-color: #6D5DF6;
            box-shadow: 0 0 0 4px rgba(109,93,246,0.10);
        }

        .ba-empty {
            padding: 28px;
            border-radius: 20px;
            background: #fafbff;
            border: 1px dashed rgba(15,23,42,0.10);
            color: #6b7280;
            font-size: 14px;
            line-height: 1.9;
            text-align: center;
        }

        .ba-pagination {
            margin-top: 18px;
        }

        @media (max-width: 1100px) {
            .ba-stats-grid,
            .ba-admin-grid,
            .ba-images-grid {
                grid-template-columns: 1fr 1fr;
            }

            .ba-admin-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .ba-stats-grid,
            .ba-admin-grid,
            .ba-images-grid {
                grid-template-columns: 1fr;
            }

            .ba-admin-hero,
            .ba-list-card,
            .ba-details-card {
                padding: 20px;
                border-radius: 20px;
            }

            .ba-admin-hero h1 {
                font-size: 28px;
            }

            .ba-details-title {
                font-size: 24px;
            }
        }
    </style>

    <div class="ba-admin-shell">
        <section class="ba-admin-hero">
            <h1>{{ $isArabic ? 'مراجعة حسابات الأعمال' : 'Review Business Accounts' }}</h1>
            <p>
                {{ $isArabic
                    ? 'يمكنك هنا مشاهدة الحسابات بشكل مختصر، ثم تحديد أي حساب لعرض التفاصيل الكاملة وقبوله أو رفضه.'
                    : 'Here you can browse accounts in a compact list, then select one to view its full details and approve or reject it.' }}
            </p>
        </section>

        <section class="ba-stats-grid">
            <div class="ba-stat-card">
                <strong>{{ $allCount }}</strong>
                <span>{{ $isArabic ? 'إجمالي الحسابات' : 'Total Accounts' }}</span>
                <small>{{ $isArabic ? 'كل الحسابات الموجودة ضمن الصفحة الحالية.' : 'All accounts in the current page.' }}</small>
            </div>

            <div class="ba-stat-card">
                <strong>{{ $pendingCount }}</strong>
                <span>{{ $isArabic ? 'قيد المراجعة' : 'Pending' }}</span>
                <small>{{ $isArabic ? 'بحاجة لمراجعة الإدارة.' : 'Waiting for admin review.' }}</small>
            </div>

            <div class="ba-stat-card">
                <strong>{{ $approvedCount }}</strong>
                <span>{{ $isArabic ? 'المقبولة' : 'Approved' }}</span>
                <small>{{ $isArabic ? 'الحسابات التي تم قبولها.' : 'Accounts already approved.' }}</small>
            </div>

            <div class="ba-stat-card">
                <strong>{{ $rejectedCount }}</strong>
                <span>{{ $isArabic ? 'المرفوضة' : 'Rejected' }}</span>
                <small>{{ $isArabic ? 'الحسابات التي تم رفضها.' : 'Accounts already rejected.' }}</small>
            </div>
        </section>

        <div class="ba-admin-tabs">
            <a href="{{ route('admin.business-accounts.index', ['status' => 'all']) }}" class="ba-admin-tab {{ $status === 'all' ? 'active' : '' }}">
                {{ $isArabic ? 'الكل' : 'All' }}
            </a>
            <a href="{{ route('admin.business-accounts.index', ['status' => 'pending']) }}" class="ba-admin-tab {{ $status === 'pending' ? 'active' : '' }}">
                {{ $isArabic ? 'قيد المراجعة' : 'Pending' }}
            </a>
            <a href="{{ route('admin.business-accounts.index', ['status' => 'approved']) }}" class="ba-admin-tab {{ $status === 'approved' ? 'active' : '' }}">
                {{ $isArabic ? 'المقبولة' : 'Approved' }}
            </a>
            <a href="{{ route('admin.business-accounts.index', ['status' => 'rejected']) }}" class="ba-admin-tab {{ $status === 'rejected' ? 'active' : '' }}">
                {{ $isArabic ? 'المرفوضة' : 'Rejected' }}
            </a>
        </div>

        @if (session('success'))
            <div class="ba-alert-success">
                {{ session('success') }}
            </div>
        @endif

        <section class="ba-admin-layout">
            <div class="ba-list-card">
                <div class="ba-card-head">
                    <h2>{{ $isArabic ? 'الحسابات الموجودة' : 'Available Accounts' }}</h2>
                    <span>{{ $filteredItems->count() }} {{ $isArabic ? 'حساب' : 'accounts' }}</span>
                </div>

                @if ($filteredItems->count())
                    <div class="ba-account-list">
                        @foreach ($filteredItems as $item)
                            <a
                                href="{{ route('admin.business-accounts.index', ['status' => $status, 'selected' => $item->id]) }}"
                                class="ba-account-link {{ $selectedItem && $selectedItem->id === $item->id ? 'active' : '' }}"
                            >
                                <h3 class="ba-account-name">{{ resolveBusinessNameAdmin($item, $isArabic) }}</h3>
                                <div class="ba-account-meta">
                                    {{ $item->user?->name ?? '—' }}<br>
                                    {{ resolveCityNameAdmin($item, $isArabic) }}
                                </div>

                                <span class="ba-status {{ businessStatusClassAdmin($item->status) }}">
                                    {{ businessStatusLabelAdmin($item->status, $isArabic) }}
                                </span>
                            </a>
                        @endforeach
                    </div>

                    <div class="ba-pagination">
                        {{ $items->withQueryString()->links() }}
                    </div>
                @else
                    <div class="ba-empty">
                        {{ $isArabic ? 'لا توجد حسابات ضمن هذا الفلتر حاليًا.' : 'There are no accounts in this filter right now.' }}
                    </div>
                @endif
            </div>

            <div class="ba-details-card">
                @if ($selectedItem)
                    <div class="ba-details-head">
                        <div>
                            <h2 class="ba-details-title">{{ resolveBusinessNameAdmin($selectedItem, $isArabic) }}</h2>
                            <div class="ba-details-sub">
                                {{ $isArabic ? 'المستخدم:' : 'User:' }}
                                {{ $selectedItem->user?->name ?? '—' }}
                                •
                                {{ $selectedItem->user?->email ?? '—' }}
                            </div>
                        </div>

                        <span class="ba-status {{ businessStatusClassAdmin($selectedItem->status) }}">
                            {{ businessStatusLabelAdmin($selectedItem->status, $isArabic) }}
                        </span>
                    </div>

                    <div class="ba-admin-grid">
                        <div class="ba-admin-box">
                            <span>{{ $isArabic ? 'نوع النشاط' : 'Activity Type' }}</span>
                            <strong>{{ resolveActivityNameAdmin($selectedItem, $isArabic) }}</strong>
                        </div>

                        <div class="ba-admin-box">
                            <span>{{ $isArabic ? 'المدينة' : 'City' }}</span>
                            <strong>{{ resolveCityNameAdmin($selectedItem, $isArabic) }}</strong>
                        </div>

                        <div class="ba-admin-box">
                            <span>{{ $isArabic ? 'رقم الرخصة' : 'License Number' }}</span>
                            <strong>{{ $selectedItem->license_number ?? '—' }}</strong>
                        </div>

                        <div class="ba-admin-box">
                            <span>{{ $isArabic ? 'تاريخ الإنشاء' : 'Created At' }}</span>
                            <strong>{{ optional($selectedItem->created_at)->format('Y-m-d') ?? '—' }}</strong>
                        </div>
                    </div>

                    @if ($selectedItem->activities)
                        <div class="ba-admin-details">
                            <strong>{{ $isArabic ? 'النشاطات:' : 'Activities:' }}</strong><br>
                            {{ $selectedItem->activities }}
                        </div>
                    @endif

                    @if ($selectedItem->details)
                        <div class="ba-admin-details">
                            <strong>{{ $isArabic ? 'التفاصيل:' : 'Details:' }}</strong><br>
                            {{ $selectedItem->details }}
                        </div>
                    @endif

                    @if ($selectedItem->images && $selectedItem->images->count())
                        <div class="ba-images-wrap">
                            <h3 class="ba-images-title">{{ $isArabic ? 'صور الحساب' : 'Account Images' }}</h3>
                            <div class="ba-images-grid">
                                @foreach ($selectedItem->images->take(6) as $image)
                                    <img src="{{ asset('storage/' . ltrim($image->path, '/')) }}" alt="business image">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($selectedItem->status === 'rejected' && $selectedItem->rejection_reason)
                        <div class="ba-admin-reason">
                            <strong>{{ $isArabic ? 'سبب الرفض:' : 'Rejection reason:' }}</strong><br>
                            {{ $selectedItem->rejection_reason }}
                        </div>
                    @endif

                    @if ($selectedItem->status === 'pending')
                        <div class="ba-admin-actions">
                            <form method="POST" action="{{ route('admin.business-accounts.approve', $selectedItem->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="{{ $status }}">
                                <button type="submit" class="ba-btn-primary">
                                    {{ $isArabic ? 'قبول الحساب' : 'Approve Account' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.business-accounts.reject', $selectedItem->id) }}" class="ba-reject-form">
                                @csrf
                                <input type="hidden" name="status" value="{{ $status }}">
                                <textarea
                                    name="rejection_reason"
                                    placeholder="{{ $isArabic ? 'اكتب سبب الرفض...' : 'Write rejection reason...' }}"
                                    required
                                ></textarea>

                                <button type="submit" class="ba-btn-danger">
                                    {{ $isArabic ? 'رفض الحساب' : 'Reject Account' }}
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="ba-empty">
                        {{ $isArabic ? 'اختر حسابًا من القائمة لعرض التفاصيل.' : 'Select an account from the list to view details.' }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection