@extends('layouts.app')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $notifications = $notifications ?? collect();
        $stats = $stats ?? [
            'total' => 0,
            'orders' => 0,
            'business_accounts' => 0,
            'latest' => null,
        ];

        $statusLabel = function ($status) use ($isArabic) {
            return match ($status) {
                'accepted', 'approved' => $isArabic ? 'مقبول' : 'Approved',
                'rejected' => $isArabic ? 'مرفوض' : 'Rejected',
                'cancelled' => $isArabic ? 'ملغي' : 'Cancelled',
                default => $isArabic ? 'قيد المراجعة' : 'Pending',
            };
        };

        $statusClass = function ($status) {
            return match ($status) {
                'accepted', 'approved' => 'notif-status-approved',
                'rejected' => 'notif-status-rejected',
                'cancelled' => 'notif-status-cancelled',
                default => 'notif-status-pending',
            };
        };
    @endphp

    <style>
        .notif-shell {
            display: grid;
            gap: 24px;
        }

        .notif-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 34px;
            background: linear-gradient(135deg, #0f172a 0%, #172554 48%, #1e3a8a 100%);
            color: #fff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
        }

        .notif-hero::before {
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

        .notif-hero-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 24px;
            align-items: end;
        }

        .notif-kicker {
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

        .notif-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #eab308;
        }

        .notif-title {
            margin: 0 0 12px;
            font-size: 42px;
            line-height: 1.02;
            letter-spacing: -0.05em;
            font-weight: 900;
        }

        .notif-copy {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .notif-hero-side {
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 28px;
            padding: 20px;
            backdrop-filter: blur(12px);
        }

        .notif-hero-side h3 {
            margin: 0 0 14px;
            font-size: 19px;
            font-weight: 800;
        }

        .notif-hero-list {
            display: grid;
            gap: 12px;
        }

        .notif-hero-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,.86);
            font-size: 14px;
        }

        .notif-hero-item strong {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
        }

        .notif-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .notif-stat-card {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 26px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
        }

        .notif-stat-label {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .notif-stat-number {
            font-size: 36px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
            margin-bottom: 6px;
        }

        .notif-stat-note {
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.8;
        }

        .notif-card {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 30px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
            padding: 24px;
        }

        .notif-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .notif-head h2 {
            margin: 0;
            font-size: 26px;
            font-weight: 900;
            color: #0f172a;
        }

        .notif-list {
            display: grid;
            gap: 14px;
        }

        .notif-item {
            padding: 18px;
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
        }

        .notif-item-top {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 12px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .notif-item-title {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
        }

        .notif-item-body {
            color: #64748b;
            font-size: 14px;
            line-height: 1.9;
            margin-bottom: 10px;
        }

        .notif-item-date {
            color: #94a3b8;
            font-size: 12px;
        }

        .notif-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .notif-status-approved {
            background: rgba(5,150,105,.10);
            color: #059669;
        }

        .notif-status-rejected {
            background: rgba(239,68,68,.10);
            color: #dc2626;
        }

        .notif-status-cancelled {
            background: rgba(100,116,139,.12);
            color: #475569;
        }

        .notif-status-pending {
            background: rgba(245,158,11,.12);
            color: #d97706;
        }

        .notif-empty {
            padding: 28px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px dashed rgba(15,23,42,.10);
            color: #64748b;
            text-align: center;
        }

        @media (max-width: 1100px) {
            .notif-hero-content,
            .notif-stats {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .notif-hero,
            .notif-card {
                padding: 20px;
                border-radius: 24px;
            }

            .notif-title {
                font-size: 32px;
            }
        }
    </style>

    <div class="notif-shell">
        <section class="notif-hero">
            <div class="notif-hero-content">
                <div>
                    <span class="notif-kicker">{{ $isArabic ? 'الإشعارات' : 'Notifications' }}</span>
                    <h1 class="notif-title">{{ $isArabic ? 'آخر التحديثات المهمة لك' : 'Your latest important updates' }}</h1>
                    <p class="notif-copy">
                        {{ $isArabic
                            ? 'من هذه الصفحة يمكنك متابعة تحديثات الطلبات وحسابات الأعمال الخاصة بك ضمن واجهة واضحة ومرتبة.'
                            : 'From this page, you can track your order and business account updates in a clear, organized interface.' }}
                    </p>
                </div>

                <div class="notif-hero-side">
                    <h3>{{ $isArabic ? 'ملخص سريع' : 'Quick summary' }}</h3>
                    <div class="notif-hero-list">
                        <div class="notif-hero-item">
                            <span>{{ $isArabic ? 'إجمالي الإشعارات' : 'Total notifications' }}</span>
                            <strong>{{ $stats['total'] ?? 0 }}</strong>
                        </div>
                        <div class="notif-hero-item">
                            <span>{{ $isArabic ? 'إشعارات الطلبات' : 'Order updates' }}</span>
                            <strong>{{ $stats['orders'] ?? 0 }}</strong>
                        </div>
                        <div class="notif-hero-item">
                            <span>{{ $isArabic ? 'إشعارات الأعمال' : 'Business updates' }}</span>
                            <strong>{{ $stats['business_accounts'] ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="notif-stats">
            <div class="notif-stat-card">
                <span class="notif-stat-label">{{ $isArabic ? 'كل الإشعارات' : 'All notifications' }}</span>
                <div class="notif-stat-number">{{ $stats['total'] ?? 0 }}</div>
                <div class="notif-stat-note">{{ $isArabic ? 'كل التحديثات المرتبطة بحسابك' : 'All updates related to your account' }}</div>
            </div>

            <div class="notif-stat-card">
                <span class="notif-stat-label">{{ $isArabic ? 'الطلبات' : 'Orders' }}</span>
                <div class="notif-stat-number">{{ $stats['orders'] ?? 0 }}</div>
                <div class="notif-stat-note">{{ $isArabic ? 'تحديثات مرتبطة بطلباتك' : 'Updates related to your service orders' }}</div>
            </div>

            <div class="notif-stat-card">
                <span class="notif-stat-label">{{ $isArabic ? 'حسابات الأعمال' : 'Business accounts' }}</span>
                <div class="notif-stat-number">{{ $stats['business_accounts'] ?? 0 }}</div>
                <div class="notif-stat-note">{{ $isArabic ? 'تحديثات مرتبطة بحسابات الأعمال' : 'Updates related to your business accounts' }}</div>
            </div>
        </section>

        <section class="notif-card">
            <div class="notif-head">
                <h2>{{ $isArabic ? 'قائمة الإشعارات' : 'Notifications list' }}</h2>
            </div>

            @if ($notifications->count())
                <div class="notif-list">
                    @foreach ($notifications as $notification)
                        <div class="notif-item">
                            <div class="notif-item-top">
                                <h3 class="notif-item-title">
                                    {{ $isArabic ? $notification['title_ar'] : $notification['title_en'] }}
                                </h3>

                                <span class="notif-status {{ $statusClass($notification['status']) }}">
                                    {{ $statusLabel($notification['status']) }}
                                </span>
                            </div>

                            <div class="notif-item-body">
                                {{ $isArabic ? $notification['body_ar'] : $notification['body_en'] }}
                            </div>

                            <div class="notif-item-date">
                                {{ optional($notification['date'])->format('Y-m-d H:i') ?? '—' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="notif-empty">
                    {{ $isArabic ? 'لا توجد إشعارات حالياً.' : 'There are no notifications right now.' }}
                </div>
            @endif
        </section>
    </div>
@endsection