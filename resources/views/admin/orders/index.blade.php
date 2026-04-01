@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $selectedOrder = $selectedOrder ?? ($orders->first() ?? null);

        $totalOrders = $stats['total'] ?? 0;
        $pendingOrders = $stats['pending'] ?? 0;
        $acceptedOrders = $stats['accepted'] ?? 0;
        $rejectedOrders = $stats['rejected'] ?? 0;
        $cancelledOrders = $stats['cancelled'] ?? 0;

        $serviceName = function ($service) use ($isArabic) {
            if (! $service) {
                return $isArabic ? 'خدمة غير معروفة' : 'Unknown service';
            }

            return $isArabic
                ? ($service->name_ar ?? $service->name_en ?? '—')
                : ($service->name_en ?? $service->name_ar ?? '—');
        };

        $businessName = function ($business) use ($isArabic) {
            if (! $business) {
                return $isArabic ? 'غير معروف' : 'Unknown';
            }

            return $isArabic
                ? ($business->name_ar ?? $business->name_en ?? '—')
                : ($business->name_en ?? $business->name_ar ?? '—');
        };

        $statusLabel = function ($status) use ($isArabic) {
            return match ($status) {
                'accepted' => $isArabic ? 'مقبول' : 'Accepted',
                'rejected' => $isArabic ? 'مرفوض' : 'Rejected',
                'cancelled' => $isArabic ? 'ملغي' : 'Cancelled',
                default => $isArabic ? 'قيد الانتظار' : 'Pending',
            };
        };

        $statusClass = function ($status) {
            return match ($status) {
                'accepted' => 'accepted',
                'rejected' => 'rejected',
                'cancelled' => 'cancelled',
                default => 'pending',
            };
        };
    @endphp

    <style>
        .orders-admin-shell {
            display: grid;
            gap: 24px;
        }

        .orders-admin-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 34px;
            background:
                linear-gradient(135deg, #0f172a 0%, #172554 48%, #1e3a8a 100%);
            color: #fff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
        }

        .orders-admin-hero::before {
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

        .orders-admin-hero::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            top: -110px;
            inset-inline-end: -80px;
            background: radial-gradient(circle, rgba(255,255,255,.16), transparent 66%);
        }

        .orders-admin-hero-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 24px;
            align-items: end;
        }

        .orders-admin-kicker {
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

        .orders-admin-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #eab308;
        }

        .orders-admin-title {
            margin: 0 0 12px;
            font-size: 46px;
            line-height: 1.02;
            letter-spacing: -0.05em;
            font-weight: 900;
        }

        .orders-admin-copy {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .orders-admin-hero-side {
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 28px;
            padding: 20px;
            backdrop-filter: blur(12px);
        }

        .orders-admin-hero-side h3 {
            margin: 0 0 14px;
            font-size: 19px;
            font-weight: 800;
        }

        .orders-admin-hero-list {
            display: grid;
            gap: 12px;
        }

        .orders-admin-hero-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,.86);
            font-size: 14px;
        }

        .orders-admin-hero-item strong {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
        }

        .orders-admin-alert-success,
        .orders-admin-alert-error {
            padding: 14px 16px;
            border-radius: 18px;
            font-size: 14px;
            font-weight: 700;
        }

        .orders-admin-alert-success {
            background: rgba(5,150,105,.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,.12);
        }

        .orders-admin-alert-error {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.12);
        }

        .orders-admin-stats {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 16px;
        }

        .order-stat-card {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 26px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
        }

        .order-stat-label {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .order-stat-number {
            font-size: 36px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
            margin-bottom: 6px;
        }

        .order-stat-note {
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.8;
        }

        .orders-admin-layout {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 20px;
            align-items: start;
        }

        .orders-panel,
        .orders-detail-panel {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 30px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
            padding: 24px;
        }

        .orders-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .orders-panel-title {
            margin: 0;
            font-size: 25px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.03em;
        }

        .orders-panel-sub {
            color: #64748b;
            font-size: 13px;
        }

        .orders-filter-form {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 12px;
            margin-bottom: 18px;
        }

        .orders-select {
            width: 100%;
            border: 1px solid rgba(15,23,42,.08);
            background: #fff;
            border-radius: 18px;
            padding: 13px 14px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .orders-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.10);
        }

        .orders-btn-primary,
        .orders-btn-secondary,
        .orders-btn-danger,
        .orders-btn-warning {
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

        .orders-btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 14px 28px rgba(37,99,235,.18);
        }

        .orders-btn-secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,.08);
        }

        .orders-btn-danger {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.12);
        }

        .orders-btn-warning {
            background: rgba(245,158,11,.12);
            color: #d97706;
            border: 1px solid rgba(245,158,11,.18);
        }

        .orders-list {
            display: grid;
            gap: 16px;
        }

        .order-card {
            position: relative;
            text-decoration: none;
            display: block;
            padding: 18px;
            border-radius: 24px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
            transition: .22s ease;
            overflow: hidden;
        }

        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 34px rgba(15,23,42,.08);
        }

        .order-card.active {
            border-color: rgba(59,130,246,.28);
            box-shadow: 0 18px 34px rgba(59,130,246,.10);
            background: linear-gradient(180deg, rgba(239,246,255,1) 0%, rgba(248,250,252,1) 100%);
        }

        .order-card.active::after {
            content: "";
            position: absolute;
            inset-inline-end: -20px;
            top: -20px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,.14), transparent 70%);
        }

        .order-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .order-card-name {
            margin: 0 0 4px;
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.15;
        }

        .order-card-sub {
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }

        .order-badge {
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

        .order-badge.pending {
            background: rgba(245,158,11,.12);
            color: #d97706;
        }

        .order-badge.accepted {
            background: rgba(5,150,105,.10);
            color: #059669;
        }

        .order-badge.rejected {
            background: rgba(239,68,68,.10);
            color: #dc2626;
        }

        .order-badge.cancelled {
            background: rgba(100,116,139,.12);
            color: #475569;
        }

        .order-card-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .order-mini-box {
            padding: 12px;
            border-radius: 16px;
            background: rgba(255,255,255,.90);
            border: 1px solid rgba(15,23,42,.06);
        }

        .order-mini-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
        }

        .order-mini-box strong {
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
        }

        .orders-pagination {
            margin-top: 18px;
        }

        .order-detail-title {
            margin: 0 0 6px;
            font-size: 32px;
            line-height: 1.05;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
        }

        .order-detail-sub {
            color: #64748b;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .order-detail-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .order-detail-box {
            padding: 14px;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
        }

        .order-detail-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .order-detail-box strong {
            display: block;
            color: #0f172a;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.7;
        }

        .order-detail-text {
            margin-top: 18px;
            padding: 16px;
            border-radius: 22px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,.06);
            color: #475569;
            font-size: 14px;
            line-height: 1.95;
        }

        .order-rating-box {
            margin-top: 18px;
            padding: 16px;
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
        }

        .order-rating-box h3 {
            margin: 0 0 12px;
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
        }

        .order-rating-box p {
            margin: 0 0 8px;
            color: #475569;
            font-size: 14px;
        }

        .order-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .orders-empty {
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
            .orders-admin-stats {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .orders-admin-hero-content,
            .orders-admin-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .order-card-grid,
            .order-detail-grid,
            .orders-filter-form {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .orders-admin-hero,
            .orders-panel,
            .orders-detail-panel {
                padding: 20px;
                border-radius: 24px;
            }

            .orders-admin-title {
                font-size: 32px;
            }

            .orders-admin-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="orders-admin-shell">
        <section class="orders-admin-hero">
            <div class="orders-admin-hero-content">
                <div>
                    <span class="orders-admin-kicker">{{ $isArabic ? 'إدارة ومتابعة الطلبات' : 'Orders control center' }}</span>

                    <h1 class="orders-admin-title">
                        {{ $isArabic ? 'واجهة أفخم لإدارة طلبات المنصة' : 'A premium admin experience for platform orders' }}
                    </h1>

                    <p class="orders-admin-copy">
                        {{ $isArabic
                            ? 'استعرض كل الطلبات داخل المنصة، فلترها حسب الحالة، وادخل مباشرة على القرار المناسب من قبول أو رفض أو حذف ضمن واجهة أوضح وأرتب.'
                            : 'Browse all platform orders, filter them by status, and take the right decision instantly with a cleaner and more premium interface.' }}
                    </p>
                </div>

                <div class="orders-admin-hero-side">
                    <h3>{{ $isArabic ? 'ملخص مباشر' : 'Direct summary' }}</h3>

                    <div class="orders-admin-hero-list">
                        <div class="orders-admin-hero-item">
                            <span>{{ $isArabic ? 'إجمالي الطلبات' : 'Total orders' }}</span>
                            <strong>{{ $totalOrders }}</strong>
                        </div>

                        <div class="orders-admin-hero-item">
                            <span>{{ $isArabic ? 'المعروضة الآن' : 'Shown now' }}</span>
                            <strong>{{ $orders->count() }}</strong>
                        </div>

                        <div class="orders-admin-hero-item">
                            <span>{{ $isArabic ? 'الصفحة الحالية' : 'Current page' }}</span>
                            <strong>{{ method_exists($orders, 'currentPage') ? $orders->currentPage() : 1 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="orders-admin-alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="orders-admin-alert-error">{{ session('error') }}</div>
        @endif

        <section class="orders-admin-stats">
            <div class="order-stat-card">
                <span class="order-stat-label">{{ $isArabic ? 'إجمالي الطلبات' : 'Total orders' }}</span>
                <div class="order-stat-number">{{ $totalOrders }}</div>
                <div class="order-stat-note">{{ $isArabic ? 'كل الطلبات المسجلة داخل النظام' : 'All orders recorded in the system' }}</div>
            </div>

            <div class="order-stat-card">
                <span class="order-stat-label">{{ $isArabic ? 'قيد الانتظار' : 'Pending' }}</span>
                <div class="order-stat-number">{{ $pendingOrders }}</div>
                <div class="order-stat-note">{{ $isArabic ? 'طلبات بانتظار قرار' : 'Orders waiting for a decision' }}</div>
            </div>

            <div class="order-stat-card">
                <span class="order-stat-label">{{ $isArabic ? 'مقبولة' : 'Accepted' }}</span>
                <div class="order-stat-number">{{ $acceptedOrders }}</div>
                <div class="order-stat-note">{{ $isArabic ? 'طلبات تمت الموافقة عليها' : 'Approved orders' }}</div>
            </div>

            <div class="order-stat-card">
                <span class="order-stat-label">{{ $isArabic ? 'مرفوضة' : 'Rejected' }}</span>
                <div class="order-stat-number">{{ $rejectedOrders }}</div>
                <div class="order-stat-note">{{ $isArabic ? 'طلبات تم رفضها' : 'Rejected orders' }}</div>
            </div>

            <div class="order-stat-card">
                <span class="order-stat-label">{{ $isArabic ? 'ملغاة' : 'Cancelled' }}</span>
                <div class="order-stat-number">{{ $cancelledOrders }}</div>
                <div class="order-stat-note">{{ $isArabic ? 'طلبات ألغيت من النظام' : 'Cancelled orders' }}</div>
            </div>
        </section>

        <section class="orders-admin-layout">
            <div class="orders-panel">
                <div class="orders-panel-head">
                    <h2 class="orders-panel-title">{{ $isArabic ? 'قائمة الطلبات' : 'Orders list' }}</h2>
                    <span class="orders-panel-sub">{{ $orders->total() ?? $orders->count() }}</span>
                </div>

                <form method="GET" action="{{ route('admin.orders.index') }}" class="orders-filter-form">
                    <select name="status" class="orders-select">
                        <option value="">{{ $isArabic ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="pending" @selected(($status ?? '') === 'pending')>{{ $isArabic ? 'قيد الانتظار' : 'Pending' }}</option>
                        <option value="accepted" @selected(($status ?? '') === 'accepted')>{{ $isArabic ? 'مقبول' : 'Accepted' }}</option>
                        <option value="rejected" @selected(($status ?? '') === 'rejected')>{{ $isArabic ? 'مرفوض' : 'Rejected' }}</option>
                        <option value="cancelled" @selected(($status ?? '') === 'cancelled')>{{ $isArabic ? 'ملغي' : 'Cancelled' }}</option>
                    </select>

                    <button type="submit" class="orders-btn-primary">
                        {{ $isArabic ? 'تطبيق' : 'Apply' }}
                    </button>

                    <a href="{{ route('admin.orders.index') }}" class="orders-btn-secondary">
                        {{ $isArabic ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </form>

                @if ($orders->count())
                    <div class="orders-list">
                        @foreach ($orders as $order)
                            <a href="{{ route('admin.orders.index', array_filter(['selected' => $order->id, 'status' => $status ?? null])) }}"
                               class="order-card {{ $selectedOrder && $selectedOrder->id === $order->id ? 'active' : '' }}">

                                <div class="order-card-top">
                                    <div>
                                        <h3 class="order-card-name">{{ $serviceName($order->service) }}</h3>
                                        <div class="order-card-sub">
                                            {{ $isArabic ? 'المستخدم:' : 'User:' }} {{ $order->user->name ?? '—' }}<br>
                                            {{ $isArabic ? 'حساب المستقبِل:' : 'Receiver:' }} {{ $businessName($order->receiverBusinessAccount) }}
                                        </div>
                                    </div>

                                    <span class="order-badge {{ $statusClass($order->status) }}">
                                        {{ $statusLabel($order->status) }}
                                    </span>
                                </div>

                                <div class="order-card-grid">
                                    <div class="order-mini-box">
                                        <span>{{ $isArabic ? 'الكمية' : 'Quantity' }}</span>
                                        <strong>{{ $order->quantity }}</strong>
                                    </div>

                                    <div class="order-mini-box">
                                        <span>{{ $isArabic ? 'التاريخ' : 'Date' }}</span>
                                        <strong>{{ optional($order->created_at)->format('Y-m-d') ?? '—' }}</strong>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="orders-pagination">
                        {{ $orders->withQueryString()->links() }}
                    </div>
                @else
                    <div class="orders-empty">
                        {{ $isArabic ? 'لا توجد طلبات حالياً.' : 'There are no orders right now.' }}
                    </div>
                @endif
            </div>

            <div class="orders-detail-panel">
                <div class="orders-panel-head">
                    <h2 class="orders-panel-title">{{ $isArabic ? 'تفاصيل الطلب المختار' : 'Selected order details' }}</h2>
                </div>

                @if ($selectedOrder)
                    <h3 class="order-detail-title">{{ $serviceName($selectedOrder->service) }}</h3>
                    <div class="order-detail-sub">
                        {{ $isArabic ? 'طلب رقم' : 'Order #' }} {{ $selectedOrder->id }}
                    </div>

                    <div class="order-detail-grid">
                        <div class="order-detail-box">
                            <span>{{ $isArabic ? 'المستخدم' : 'User' }}</span>
                            <strong>{{ $selectedOrder->user->name ?? '—' }}</strong>
                        </div>

                        <div class="order-detail-box">
                            <span>{{ $isArabic ? 'حساب المرسل' : 'Sender account' }}</span>
                            <strong>{{ $businessName($selectedOrder->senderBusinessAccount) }}</strong>
                        </div>

                        <div class="order-detail-box">
                            <span>{{ $isArabic ? 'حساب المستقبِل' : 'Receiver account' }}</span>
                            <strong>{{ $businessName($selectedOrder->receiverBusinessAccount) }}</strong>
                        </div>

                        <div class="order-detail-box">
                            <span>{{ $isArabic ? 'الكمية' : 'Quantity' }}</span>
                            <strong>{{ $selectedOrder->quantity }}</strong>
                        </div>

                        <div class="order-detail-box">
                            <span>{{ $isArabic ? 'الحالة' : 'Status' }}</span>
                            <strong>{{ $statusLabel($selectedOrder->status) }}</strong>
                        </div>

                        <div class="order-detail-box">
                            <span>{{ $isArabic ? 'وقت الحاجة' : 'Needed at' }}</span>
                            <strong>{{ optional($selectedOrder->needed_at)->format('Y-m-d H:i') ?? '—' }}</strong>
                        </div>
                    </div>

                    <div class="order-detail-text">
                        <strong>{{ $isArabic ? 'تفاصيل الطلب:' : 'Order details:' }}</strong><br>
                        {{ $selectedOrder->details ?: ($isArabic ? 'لا توجد تفاصيل إضافية.' : 'No extra details provided.') }}
                    </div>

                    @if ($selectedOrder->rating)
                        <div class="order-rating-box">
                            <h3>{{ $isArabic ? 'التقييم المرتبط' : 'Related rating' }}</h3>
                            <p><strong>{{ $isArabic ? 'العلامة:' : 'Score:' }}</strong> {{ $selectedOrder->rating->score }}/5</p>
                            <p><strong>{{ $isArabic ? 'التعليق:' : 'Comment:' }}</strong> {{ $selectedOrder->rating->comment ?: '—' }}</p>
                        </div>
                    @endif

                    <div class="order-actions">
                        @if ($selectedOrder->status === 'pending')
                            <form method="POST" action="{{ route('admin.orders.accept', $selectedOrder->id) }}">
                                @csrf
                                <button type="submit" class="orders-btn-primary">
                                    {{ $isArabic ? 'قبول الطلب' : 'Accept order' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.orders.reject', $selectedOrder->id) }}">
                                @csrf
                                <button type="submit" class="orders-btn-warning">
                                    {{ $isArabic ? 'رفض الطلب' : 'Reject order' }}
                                </button>
                            </form>
                        @endif

                        <form method="POST"
                              action="{{ route('admin.orders.destroy', $selectedOrder->id) }}"
                              onsubmit="return confirm('{{ $isArabic ? 'هل أنت متأكد من حذف الطلب؟' : 'Are you sure you want to delete this order?' }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="orders-btn-danger">
                                {{ $isArabic ? 'حذف الطلب' : 'Delete order' }}
                            </button>
                        </form>
                    </div>
                @else
                    <div class="orders-empty">
                        {{ $isArabic ? 'اختر طلباً من القائمة لعرض التفاصيل.' : 'Select an order from the list to view details.' }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection