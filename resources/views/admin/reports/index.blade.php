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
        $resolvedCount = $pageItems->where('status', 'resolved')->count();
        $rejectedCount = $pageItems->where('status', 'rejected')->count();

        function resolveReportServiceName($report, $isArabic) {
            $service = $report?->service;
            if (! $service) return '—';

            return $isArabic
                ? ($service->name_ar ?? $service->name_en ?? '—')
                : ($service->name_en ?? $service->name_ar ?? '—');
        }

        function resolveReportProviderName($report, $isArabic) {
            $service = $report?->service;
            if (! $service || ! $service->businessAccount) {
                return $isArabic ? 'مزود غير معروف' : 'Unknown provider';
            }

            return $isArabic
                ? ($service->businessAccount->name_ar ?? $service->businessAccount->name_en ?? '—')
                : ($service->businessAccount->name_en ?? $service->businessAccount->name_ar ?? '—');
        }

        function reportStatusLabel($statusValue, $isArabic) {
            return match ($statusValue) {
                'resolved' => $isArabic ? 'تمت المعالجة' : 'Resolved',
                'rejected' => $isArabic ? 'مرفوض' : 'Rejected',
                default => $isArabic ? 'قيد المراجعة' : 'Pending',
            };
        }

        function reportStatusClass($statusValue) {
            return match ($statusValue) {
                'resolved' => 'rp-status-resolved',
                'rejected' => 'rp-status-rejected',
                default => 'rp-status-pending',
            };
        }

        function resolveReportImage($report) {
            $image = $report?->service?->primaryImage()?->path;

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
        .reports-shell { display:grid; gap:22px; }
        .reports-hero {
            border-radius: 28px;
            padding: 28px;
            background: linear-gradient(135deg, #151d38 0%, #23346f 100%);
            color: white;
            box-shadow: 0 24px 54px rgba(24, 40, 72, 0.22);
        }
        .reports-hero h1 {
            margin: 0 0 10px;
            font-size: 34px;
            line-height: 1.08;
            font-weight: 800;
        }
        .reports-hero p {
            margin: 0;
            color: rgba(255,255,255,0.84);
            font-size: 14px;
            line-height: 1.9;
            max-width: 760px;
        }
        .reports-stats {
            display:grid;
            grid-template-columns: repeat(4, minmax(0,1fr));
            gap:14px;
        }
        .reports-stat {
            background:white;
            border-radius:20px;
            padding:18px;
            border:1px solid rgba(15,23,42,0.06);
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
        }
        .reports-stat strong {
            display:block;
            font-size:28px;
            font-weight:800;
            color:#111827;
            margin-bottom:6px;
        }
        .reports-stat span {
            display:block;
            color:#475569;
            font-size:13px;
            font-weight:700;
        }
        .reports-tabs {
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }
        .reports-tab {
            height:40px;
            padding:0 14px;
            border-radius:999px;
            background:white;
            border:1px solid rgba(15,23,42,0.08);
            color:#475569;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            font-size:13px;
            font-weight:800;
        }
        .reports-tab.active {
            background: linear-gradient(135deg, #6D5DF6, #8d81ff);
            color:white;
            border-color:transparent;
        }
        .reports-alert {
            padding:14px 16px;
            border-radius:16px;
            background: rgba(5,150,105,0.10);
            color:#059669;
            border:1px solid rgba(5,150,105,0.12);
            font-size:14px;
            font-weight:700;
        }
        .reports-layout {
            display:grid;
            grid-template-columns:360px 1fr;
            gap:20px;
            align-items:start;
        }
        .reports-list-card,
        .reports-details-card {
            background:white;
            border-radius:24px;
            padding:22px;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
            border:1px solid rgba(15,23,42,0.06);
        }
        .reports-card-head {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            flex-wrap:wrap;
            margin-bottom:18px;
        }
        .reports-card-head h2 {
            margin:0;
            font-size:24px;
            font-weight:800;
            color:#111827;
        }
        .reports-list {
            display:grid;
            gap:12px;
        }
        .reports-link {
            display:block;
            text-decoration:none;
            padding:16px;
            border-radius:18px;
            background:#fafbff;
            border:1px solid rgba(15,23,42,0.06);
        }
        .reports-link.active {
            border-color: rgba(109,93,246,0.30);
            background: linear-gradient(180deg, rgba(109,93,246,0.07), rgba(109,93,246,0.03));
        }
        .reports-link h3 {
            margin:0 0 8px;
            font-size:17px;
            font-weight:800;
            color:#111827;
        }
        .reports-link div {
            color:#6b7280;
            font-size:13px;
            line-height:1.8;
        }
        .rp-status {
            margin-top:10px;
            height:30px;
            padding:0 12px;
            border-radius:999px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            font-size:12px;
            font-weight:800;
        }
        .rp-status-pending {
            background: rgba(245,158,11,0.12);
            color:#d97706;
        }
        .rp-status-resolved {
            background: rgba(5,150,105,0.10);
            color:#059669;
        }
        .rp-status-rejected {
            background: rgba(239,68,68,0.10);
            color:#dc2626;
        }
        .report-main-image img {
            width:100%;
            max-width:420px;
            height:260px;
            object-fit:cover;
            border-radius:20px;
            display:block;
            margin-bottom:16px;
            border:1px solid rgba(15,23,42,0.06);
        }
        .report-box {
            padding:14px 16px;
            border-radius:18px;
            background:#fafbff;
            border:1px solid rgba(15,23,42,0.06);
            margin-bottom:12px;
            color:#4b5563;
            font-size:14px;
            line-height:1.9;
        }
        .report-box strong {
            display:block;
            margin-bottom:6px;
            color:#111827;
        }
        .report-actions {
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            margin-top:14px;
        }
        .report-btn {
            height:42px;
            padding:0 16px;
            border-radius:999px;
            border:none;
            cursor:pointer;
            font-size:13px;
            font-weight:800;
        }
        .report-btn-resolve {
            background: linear-gradient(135deg, #6D5DF6, #8d81ff);
            color:white;
        }
        .report-btn-reject {
            background: rgba(239,68,68,0.10);
            color:#dc2626;
            border:1px solid rgba(239,68,68,0.12);
        }
        .reports-empty {
            padding:28px;
            border-radius:20px;
            background:#fafbff;
            border:1px dashed rgba(15,23,42,0.10);
            color:#6b7280;
            font-size:14px;
            line-height:1.9;
            text-align:center;
        }
        @media (max-width: 1100px) {
            .reports-stats,
            .reports-layout {
                grid-template-columns:1fr;
            }
        }
    </style>

    <div class="reports-shell">
        <section class="reports-hero">
            <h1>{{ $isArabic ? 'إدارة البلاغات' : 'Manage Reports' }}</h1>
            <p>{{ $isArabic ? 'راجع البلاغات الواردة على الخدمات وحدد إن كانت صحيحة وتمت معالجتها أو يجب رفضها.' : 'Review incoming service reports and mark them as resolved or rejected.' }}</p>
        </section>

        <section class="reports-stats">
            <div class="reports-stat"><strong>{{ $allCount }}</strong><span>{{ $isArabic ? 'إجمالي البلاغات' : 'Total Reports' }}</span></div>
            <div class="reports-stat"><strong>{{ $pendingCount }}</strong><span>{{ $isArabic ? 'قيد المراجعة' : 'Pending' }}</span></div>
            <div class="reports-stat"><strong>{{ $resolvedCount }}</strong><span>{{ $isArabic ? 'تمت المعالجة' : 'Resolved' }}</span></div>
            <div class="reports-stat"><strong>{{ $rejectedCount }}</strong><span>{{ $isArabic ? 'مرفوضة' : 'Rejected' }}</span></div>
        </section>

        <div class="reports-tabs">
            <a href="{{ route('admin.reports.index', ['status' => 'all']) }}" class="reports-tab {{ $status === 'all' ? 'active' : '' }}">{{ $isArabic ? 'الكل' : 'All' }}</a>
            <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="reports-tab {{ $status === 'pending' ? 'active' : '' }}">{{ $isArabic ? 'قيد المراجعة' : 'Pending' }}</a>
            <a href="{{ route('admin.reports.index', ['status' => 'resolved']) }}" class="reports-tab {{ $status === 'resolved' ? 'active' : '' }}">{{ $isArabic ? 'تمت المعالجة' : 'Resolved' }}</a>
            <a href="{{ route('admin.reports.index', ['status' => 'rejected']) }}" class="reports-tab {{ $status === 'rejected' ? 'active' : '' }}">{{ $isArabic ? 'مرفوضة' : 'Rejected' }}</a>
        </div>

        @if (session('success'))
            <div class="reports-alert">{{ session('success') }}</div>
        @endif

        <section class="reports-layout">
            <div class="reports-list-card">
                <div class="reports-card-head">
                    <h2>{{ $isArabic ? 'البلاغات' : 'Reports' }}</h2>
                    <span>{{ $filteredItems->count() }}</span>
                </div>

                @if ($filteredItems->count())
                    <div class="reports-list">
                        @foreach ($filteredItems as $item)
                            <a href="{{ route('admin.reports.index', ['status' => $status, 'selected' => $item->id]) }}" class="reports-link {{ $selectedItem && $selectedItem->id === $item->id ? 'active' : '' }}">
                                <h3>{{ resolveReportServiceName($item, $isArabic) }}</h3>
                                <div>{{ $item->user?->name ?? '—' }}</div>
                                <span class="rp-status {{ reportStatusClass($item->status) }}">
                                    {{ reportStatusLabel($item->status, $isArabic) }}
                                </span>
                            </a>
                        @endforeach
                    </div>

                    <div style="margin-top:18px;">
                        {{ $items->withQueryString()->links() }}
                    </div>
                @else
                    <div class="reports-empty">{{ $isArabic ? 'لا توجد بلاغات ضمن هذا الفلتر.' : 'No reports in this filter.' }}</div>
                @endif
            </div>

            <div class="reports-details-card">
                @if ($selectedItem)
                    <div class="report-main-image">
                        <img src="{{ resolveReportImage($selectedItem) }}" alt="service image">
                    </div>

                    <div class="report-box">
                        <strong>{{ $isArabic ? 'الخدمة' : 'Service' }}</strong>
                        {{ resolveReportServiceName($selectedItem, $isArabic) }}
                    </div>

                    <div class="report-box">
                        <strong>{{ $isArabic ? 'مزود الخدمة' : 'Provider' }}</strong>
                        {{ resolveReportProviderName($selectedItem, $isArabic) }}
                    </div>

                    <div class="report-box">
                        <strong>{{ $isArabic ? 'المبلّغ' : 'Reported by' }}</strong>
                        {{ $selectedItem->user?->name ?? '—' }} — {{ $selectedItem->user?->email ?? '—' }}
                    </div>

                    <div class="report-box">
                        <strong>{{ $isArabic ? 'سبب البلاغ' : 'Report reason' }}</strong>
                        {{ $selectedItem->reason }}
                    </div>

                    <div class="report-box">
                        <strong>{{ $isArabic ? 'الحالة' : 'Status' }}</strong>
                        {{ reportStatusLabel($selectedItem->status, $isArabic) }}
                    </div>

                    @if ($selectedItem->reviewedBy)
                        <div class="report-box">
                            <strong>{{ $isArabic ? 'تمت المراجعة بواسطة' : 'Reviewed by' }}</strong>
                            {{ $selectedItem->reviewedBy->name ?? '—' }}
                        </div>
                    @endif

                    @if ($selectedItem->status === 'pending')
                        <div class="report-actions">
                            <form method="POST" action="{{ route('admin.reports.resolve', $selectedItem->id) }}">
                                @csrf
                                <input type="hidden" name="current_status" value="{{ $status }}">
                                <input type="hidden" name="status" value="resolved">
                                <button type="submit" class="report-btn report-btn-resolve">
                                    {{ $isArabic ? 'تمت المعالجة' : 'Mark as resolved' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.reports.resolve', $selectedItem->id) }}">
                                @csrf
                                <input type="hidden" name="current_status" value="{{ $status }}">
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="report-btn report-btn-reject">
                                    {{ $isArabic ? 'رفض البلاغ' : 'Reject report' }}
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="reports-empty">{{ $isArabic ? 'اختر بلاغًا من القائمة لعرض التفاصيل.' : 'Select a report from the list to view details.' }}</div>
                @endif
            </div>
        </section>
    </div>
@endsection