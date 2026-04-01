@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';

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

        $statusLabel = match ($order->status) {
            'accepted' => $isArabic ? 'مقبول' : 'Accepted',
            'rejected' => $isArabic ? 'مرفوض' : 'Rejected',
            'cancelled' => $isArabic ? 'ملغي' : 'Cancelled',
            default => $isArabic ? 'قيد الانتظار' : 'Pending',
        };

        $statusClass = match ($order->status) {
            'accepted' => 'accepted',
            'rejected' => 'rejected',
            'cancelled' => 'cancelled',
            default => 'pending',
        };
    @endphp

    <style>
        .order-show-shell {
            display: grid;
            gap: 24px;
        }

        .order-show-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 34px;
            background:
                linear-gradient(135deg, #0f172a 0%, #172554 48%, #1e3a8a 100%);
            color: #fff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
        }

        .order-show-hero::before {
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

        .order-show-hero::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            top: -110px;
            inset-inline-end: -80px;
            background: radial-gradient(circle, rgba(255,255,255,.16), transparent 66%);
        }

        .order-show-hero-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 24px;
            align-items: end;
        }

        .order-show-kicker {
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

        .order-show-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #eab308;
        }

        .order-show-title {
            margin: 0 0 12px;
            font-size: 42px;
            line-height: 1.03;
            letter-spacing: -0.05em;
            font-weight: 900;
        }

        .order-show-copy {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .order-show-hero-side {
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 28px;
            padding: 20px;
            backdrop-filter: blur(12px);
        }

        .order-show-hero-side h3 {
            margin: 0 0 14px;
            font-size: 19px;
            font-weight: 800;
        }

        .order-show-hero-list {
            display: grid;
            gap: 12px;
        }

        .order-show-hero-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,.86);
            font-size: 14px;
        }

        .order-show-hero-item strong {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
        }

        .order-show-alert-success,
        .order-show-alert-error {
            padding: 14px 16px;
            border-radius: 18px;
            font-size: 14px;
            font-weight: 700;
        }

        .order-show-alert-success {
            background: rgba(5,150,105,.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,.12);
        }

        .order-show-alert-error {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.12);
        }

        .order-show-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: start;
        }

        .order-show-panel {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 30px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
            padding: 24px;
        }

        .order-show-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .order-show-panel-title {
            margin: 0;
            font-size: 25px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.03em;
        }

        .order-show-status {
            height: 34px;
            padding: 0 14px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .order-show-status.pending {
            background: rgba(245,158,11,.12);
            color: #d97706;
        }

        .order-show-status.accepted {
            background: rgba(5,150,105,.10);
            color: #059669;
        }

        .order-show-status.rejected {
            background: rgba(239,68,68,.10);
            color: #dc2626;
        }

        .order-show-status.cancelled {
            background: rgba(100,116,139,.12);
            color: #475569;
        }

        .order-show-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .order-show-box {
            padding: 14px;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
        }

        .order-show-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .order-show-box strong {
            display: block;
            color: #0f172a;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.7;
        }

        .order-show-description,
        .order-show-rating {
            margin-top: 18px;
            padding: 16px;
            border-radius: 22px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,.06);
        }

        .order-show-description h3,
        .order-show-rating h3 {
            margin: 0 0 12px;
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
        }

        .order-show-description p,
        .order-show-rating p {
            margin: 0 0 8px;
            color: #475569;
            font-size: 14px;
            line-height: 1.9;
        }

        .order-show-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .order-btn-primary,
        .order-btn-secondary,
        .order-btn-danger,
        .order-btn-warning {
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

        .order-btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 14px 28px rgba(37,99,235,.18);
        }

        .order-btn-secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,.08);
        }

        .order-btn-danger {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.12);
        }

        .order-btn-warning {
            background: rgba(245,158,11,.12);
            color: #d97706;
            border: 1px solid rgba(245,158,11,.18);
        }

        @media (max-width: 1200px) {
            .order-show-hero-content,
            .order-show-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .order-show-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .order-show-hero,
            .order-show-panel {
                padding: 20px;
                border-radius: 24px;
            }

            .order-show-title {
                font-size: 32px;
            }
        }
    </style>

    <div class="order-show-shell">
        <section class="order-show-hero">
            <div class="order-show-hero-content">
                <div>
                    <span class="order-show-kicker">{{ $isArabic ? 'تفاصيل الطلب الإداري' : 'Admin order details' }}</span>

                    <h1 class="order-show-title">
                        {{ $serviceName($order->service) }}
                    </h1>

                    <p class="order-show-copy">
                        {{ $isArabic
                            ? 'راجع بيانات الطلب بالكامل، وقرر مباشرة إن كنت تريد قبوله أو رفضه أو حذفه من النظام.'
                            : 'Review the full order information and decide whether to accept, reject, or delete it from the system.' }}
                    </p>
                </div>

                <div class="order-show-hero-side">
                    <h3>{{ $isArabic ? 'ملخص سريع' : 'Quick summary' }}</h3>

                    <div class="order-show-hero-list">
                        <div class="order-show-hero-item">
                            <span>{{ $isArabic ? 'رقم الطلب' : 'Order ID' }}</span>
                            <strong>#{{ $order->id }}</strong>
                        </div>

                        <div class="order-show-hero-item">
                            <span>{{ $isArabic ? 'الحالة الحالية' : 'Current status' }}</span>
                            <strong>{{ $statusLabel }}</strong>
                        </div>

                        <div class="order-show-hero-item">
                            <span>{{ $isArabic ? 'تاريخ الإنشاء' : 'Created date' }}</span>
                            <strong>{{ optional($order->created_at)->format('Y-m-d') ?? '—' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="order-show-alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="order-show-alert-error">{{ session('error') }}</div>
        @endif

        <section class="order-show-layout">
            <div class="order-show-panel">
                <div class="order-show-panel-head">
                    <h2 class="order-show-panel-title">{{ $isArabic ? 'البيانات الأساسية' : 'Basic information' }}</h2>
                    <span class="order-show-status {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                <div class="order-show-grid">
                    <div class="order-show-box">
                        <span>{{ $isArabic ? 'الخدمة' : 'Service' }}</span>
                        <strong>{{ $serviceName($order->service) }}</strong>
                    </div>

                    <div class="order-show-box">
                        <span>{{ $isArabic ? 'المستخدم' : 'User' }}</span>
                        <strong>{{ $order->user->name ?? '—' }}</strong>
                    </div>

                    <div class="order-show-box">
                        <span>{{ $isArabic ? 'حساب المرسل' : 'Sender account' }}</span>
                        <strong>{{ $businessName($order->senderBusinessAccount) }}</strong>
                    </div>

                    <div class="order-show-box">
                        <span>{{ $isArabic ? 'حساب المستقبِل' : 'Receiver account' }}</span>
                        <strong>{{ $businessName($order->receiverBusinessAccount) }}</strong>
                    </div>

                    <div class="order-show-box">
                        <span>{{ $isArabic ? 'الكمية' : 'Quantity' }}</span>
                        <strong>{{ $order->quantity }}</strong>
                    </div>

                    <div class="order-show-box">
                        <span>{{ $isArabic ? 'وقت الحاجة' : 'Needed at' }}</span>
                        <strong>{{ optional($order->needed_at)->format('Y-m-d H:i') ?? '—' }}</strong>
                    </div>
                </div>

                <div class="order-show-description">
                    <h3>{{ $isArabic ? 'تفاصيل الطلب' : 'Order details' }}</h3>
                    <p>{{ $order->details ?: ($isArabic ? 'لا توجد تفاصيل إضافية.' : 'No extra details provided.') }}</p>
                </div>
            </div>

            <div class="order-show-panel">
                <div class="order-show-panel-head">
                    <h2 class="order-show-panel-title">{{ $isArabic ? 'الإجراءات' : 'Actions' }}</h2>
                </div>

                @if ($order->rating)
                    <div class="order-show-rating">
                        <h3>{{ $isArabic ? 'التقييم المرتبط' : 'Related rating' }}</h3>
                        <p><strong>{{ $isArabic ? 'العلامة:' : 'Score:' }}</strong> {{ $order->rating->score }}/5</p>
                        <p><strong>{{ $isArabic ? 'التعليق:' : 'Comment:' }}</strong> {{ $order->rating->comment ?: '—' }}</p>
                    </div>
                @endif

                <div class="order-show-actions">
                    <a href="{{ route('admin.orders.index') }}" class="order-btn-secondary">
                        {{ $isArabic ? 'الرجوع للقائمة' : 'Back to list' }}
                    </a>

                    @if ($order->status === 'pending')
                        <form method="POST" action="{{ route('admin.orders.accept', $order->id) }}">
                            @csrf
                            <button type="submit" class="order-btn-primary">
                                {{ $isArabic ? 'قبول الطلب' : 'Accept order' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.orders.reject', $order->id) }}">
                            @csrf
                            <button type="submit" class="order-btn-warning">
                                {{ $isArabic ? 'رفض الطلب' : 'Reject order' }}
                            </button>
                        </form>
                    @endif

                    <form method="POST"
                          action="{{ route('admin.orders.destroy', $order->id) }}"
                          onsubmit="return confirm('{{ $isArabic ? 'هل أنت متأكد من حذف الطلب؟' : 'Are you sure you want to delete this order?' }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="order-btn-danger">
                            {{ $isArabic ? 'حذف الطلب' : 'Delete order' }}
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection