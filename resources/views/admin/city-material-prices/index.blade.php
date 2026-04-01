@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $selectedPrice = $selectedPrice ?? null;

        $totalPrices = $prices->total();
        $activePrices = $prices->getCollection()->where('is_active', true)->count();
        $inactivePrices = $prices->getCollection()->where('is_active', false)->count();
        $selectedCityName = $selectedPrice?->city
            ? ($isArabic
                ? ($selectedPrice->city->name_ar ?? $selectedPrice->city->name_en ?? '—')
                : ($selectedPrice->city->name_en ?? $selectedPrice->city->name_ar ?? '—'))
            : '—';
    @endphp

    <style>
        .cmp-shell {
            display: grid;
            gap: 24px;
        }

        .cmp-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 34px;
            background:
                linear-gradient(135deg, #0f172a 0%, #172554 48%, #1e3a8a 100%);
            color: #fff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
        }

        .cmp-hero::before {
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

        .cmp-hero::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            top: -110px;
            inset-inline-end: -80px;
            background: radial-gradient(circle, rgba(255,255,255,.16), transparent 66%);
        }

        .cmp-hero-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 24px;
            align-items: end;
        }

        .cmp-kicker {
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

        .cmp-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #eab308;
        }

        .cmp-title {
            margin: 0 0 12px;
            font-size: 46px;
            line-height: 1.02;
            letter-spacing: -0.05em;
            font-weight: 900;
        }

        .cmp-copy {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .cmp-hero-side {
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 28px;
            padding: 20px;
            backdrop-filter: blur(12px);
        }

        .cmp-hero-side h3 {
            margin: 0 0 14px;
            font-size: 19px;
            font-weight: 800;
        }

        .cmp-hero-list {
            display: grid;
            gap: 12px;
        }

        .cmp-hero-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,.86);
            font-size: 14px;
        }

        .cmp-hero-item strong {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
        }

        .cmp-alert {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(5,150,105,.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,.12);
            font-size: 14px;
            font-weight: 700;
        }

        .cmp-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .cmp-stat-card {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 26px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
        }

        .cmp-stat-label {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .cmp-stat-number {
            font-size: 36px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
            margin-bottom: 6px;
        }

        .cmp-stat-note {
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.8;
        }

        .cmp-layout {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 20px;
            align-items: start;
        }

        .cmp-panel,
        .cmp-form-panel,
        .cmp-detail-panel {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 30px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
            padding: 24px;
        }

        .cmp-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .cmp-panel-title {
            margin: 0;
            font-size: 25px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.03em;
        }

        .cmp-panel-sub {
            color: #64748b;
            font-size: 13px;
        }

        .cmp-cards-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .cmp-card {
            display: block;
            text-decoration: none;
            padding: 18px;
            border-radius: 24px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
            transition: .22s ease;
        }

        .cmp-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 34px rgba(15,23,42,.08);
        }

        .cmp-card.active {
            border-color: rgba(59,130,246,.28);
            box-shadow: 0 18px 34px rgba(59,130,246,.10);
            background: linear-gradient(180deg, rgba(239,246,255,1) 0%, rgba(248,250,252,1) 100%);
        }

        .cmp-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .cmp-card-name {
            margin: 0 0 4px;
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.15;
        }

        .cmp-card-sub {
            color: #64748b;
            font-size: 13px;
        }

        .cmp-badge {
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

        .cmp-badge.active {
            background: rgba(5,150,105,.10);
            color: #059669;
        }

        .cmp-badge.inactive {
            background: rgba(239,68,68,.10);
            color: #dc2626;
        }

        .cmp-card-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .cmp-mini-box {
            padding: 12px;
            border-radius: 16px;
            background: rgba(255,255,255,.90);
            border: 1px solid rgba(15,23,42,.06);
        }

        .cmp-mini-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
        }

        .cmp-mini-box strong {
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
        }

        .cmp-pagination {
            margin-top: 18px;
        }

        .cmp-detail-title {
            margin: 0 0 6px;
            font-size: 30px;
            line-height: 1.05;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
        }

        .cmp-detail-sub {
            color: #64748b;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .cmp-detail-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .cmp-detail-box {
            padding: 14px;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
        }

        .cmp-detail-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .cmp-detail-box strong {
            display: block;
            color: #0f172a;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.7;
        }

        .cmp-form {
            display: grid;
            gap: 16px;
        }

        .cmp-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .cmp-group {
            display: grid;
            gap: 8px;
        }

        .cmp-label {
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .cmp-input,
        .cmp-select {
            width: 100%;
            border: 1px solid rgba(15,23,42,.08);
            background: #fff;
            border-radius: 18px;
            padding: 13px 14px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .cmp-input:focus,
        .cmp-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.10);
        }

        .cmp-error {
            color: #dc2626;
            font-size: 12px;
        }

        .cmp-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .cmp-btn-primary,
        .cmp-btn-secondary,
        .cmp-btn-danger {
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

        .cmp-btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 14px 28px rgba(37,99,235,.18);
        }

        .cmp-btn-secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,.08);
        }

        .cmp-btn-danger {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.12);
        }

        .cmp-empty {
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
            .cmp-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .cmp-hero-content,
            .cmp-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .cmp-cards-grid,
            .cmp-detail-grid,
            .cmp-form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .cmp-hero,
            .cmp-panel,
            .cmp-detail-panel,
            .cmp-form-panel {
                padding: 20px;
                border-radius: 24px;
            }

            .cmp-title {
                font-size: 32px;
            }

            .cmp-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="cmp-shell">
        <section class="cmp-hero">
            <div class="cmp-hero-content">
                <div>
                    <span class="cmp-kicker">{{ $isArabic ? 'إدارة أسعار المواد' : 'Material prices management' }}</span>

                    <h1 class="cmp-title">
                        {{ $isArabic ? 'واجهة احترافية لإدارة أسعار المواد حسب المدينة' : 'A premium interface for managing material prices by city' }}
                    </h1>

                    <p class="cmp-copy">
                        {{ $isArabic
                            ? 'من هنا يمكنك تحديد سعر كل مادة داخل كل محافظة أو مدينة، وتفعيلها لاستخدامها مباشرة داخل نظام التقدير الذكي.'
                            : 'From here you can manage the price of each material per city and activate it for direct use in the smart estimation module.' }}
                    </p>
                </div>

                <div class="cmp-hero-side">
                    <h3>{{ $isArabic ? 'ملخص مباشر' : 'Direct summary' }}</h3>

                    <div class="cmp-hero-list">
                        <div class="cmp-hero-item">
                            <span>{{ $isArabic ? 'إجمالي الأسعار' : 'Total prices' }}</span>
                            <strong>{{ $totalPrices }}</strong>
                        </div>

                        <div class="cmp-hero-item">
                            <span>{{ $isArabic ? 'المفعلة' : 'Active' }}</span>
                            <strong>{{ $activePrices }}</strong>
                        </div>

                        <div class="cmp-hero-item">
                            <span>{{ $isArabic ? 'غير المفعلة' : 'Inactive' }}</span>
                            <strong>{{ $inactivePrices }}</strong>
                        </div>

                        <div class="cmp-hero-item">
                            <span>{{ $isArabic ? 'المدينة المختارة' : 'Selected city' }}</span>
                            <strong>{{ $selectedCityName }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="cmp-alert">{{ session('success') }}</div>
        @endif

        <section class="cmp-stats">
            <div class="cmp-stat-card">
                <span class="cmp-stat-label">{{ $isArabic ? 'إجمالي الأسعار' : 'Total prices' }}</span>
                <div class="cmp-stat-number">{{ $totalPrices }}</div>
                <div class="cmp-stat-note">{{ $isArabic ? 'كل أسعار المواد المسجلة في النظام' : 'All material prices recorded in the system' }}</div>
            </div>

            <div class="cmp-stat-card">
                <span class="cmp-stat-label">{{ $isArabic ? 'الأسعار المفعلة' : 'Active prices' }}</span>
                <div class="cmp-stat-number">{{ $activePrices }}</div>
                <div class="cmp-stat-note">{{ $isArabic ? 'الأسعار المستخدمة فعليًا في التقدير' : 'Prices currently used by estimations' }}</div>
            </div>

            <div class="cmp-stat-card">
                <span class="cmp-stat-label">{{ $isArabic ? 'الأسعار غير المفعلة' : 'Inactive prices' }}</span>
                <div class="cmp-stat-number">{{ $inactivePrices }}</div>
                <div class="cmp-stat-note">{{ $isArabic ? 'أسعار محفوظة لكنها غير مستخدمة' : 'Saved prices that are not active' }}</div>
            </div>

            <div class="cmp-stat-card">
                <span class="cmp-stat-label">{{ $isArabic ? 'المواد المتاحة' : 'Available materials' }}</span>
                <div class="cmp-stat-number">{{ $materialTypes->count() }}</div>
                <div class="cmp-stat-note">{{ $isArabic ? 'عدد المواد التي يمكن تسعيرها' : 'Number of materials available for pricing' }}</div>
            </div>
        </section>

        <section class="cmp-layout">
            <div class="cmp-panel">
                <div class="cmp-panel-head">
                    <h2 class="cmp-panel-title">{{ $isArabic ? 'قائمة الأسعار' : 'Prices list' }}</h2>
                    <span class="cmp-panel-sub">{{ $prices->total() }}</span>
                </div>

                @if ($prices->count())
                    <div class="cmp-cards-grid">
                        @foreach ($prices as $price)
                            @php
                                $cityName = $isArabic
                                    ? ($price->city->name_ar ?? $price->city->name_en ?? '—')
                                    : ($price->city->name_en ?? $price->city->name_ar ?? '—');

                                $materialName = $isArabic
                                    ? ($price->materialType->name_ar ?? $price->materialType->name_en ?? '—')
                                    : ($price->materialType->name_en ?? $price->materialType->name_ar ?? '—');
                            @endphp

                            <a href="{{ route('admin.city-material-prices.index', ['selected' => $price->id]) }}"
                               class="cmp-card {{ $selectedPrice && $selectedPrice->id === $price->id ? 'active' : '' }}">
                                <div class="cmp-card-top">
                                    <div>
                                        <h3 class="cmp-card-name">{{ $materialName }}</h3>
                                        <div class="cmp-card-sub">{{ $cityName }}</div>
                                    </div>

                                    <span class="cmp-badge {{ $price->is_active ? 'active' : 'inactive' }}">
                                        {{ $price->is_active
                                            ? ($isArabic ? 'مفعل' : 'Active')
                                            : ($isArabic ? 'غير مفعل' : 'Inactive') }}
                                    </span>
                                </div>

                                <div class="cmp-card-grid">
                                    <div class="cmp-mini-box">
                                        <span>{{ $isArabic ? 'السعر' : 'Price' }}</span>
                                        <strong>{{ number_format((float) $price->price, 2) }}</strong>
                                    </div>

                                    <div class="cmp-mini-box">
                                        <span>{{ $isArabic ? 'العملة' : 'Currency' }}</span>
                                        <strong>{{ $price->currency ?? '—' }}</strong>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="cmp-pagination">
                        {{ $prices->withQueryString()->links() }}
                    </div>
                @else
                    <div class="cmp-empty">
                        {{ $isArabic ? 'لا توجد أسعار مواد حالياً.' : 'There are no material prices yet.' }}
                    </div>
                @endif
            </div>

            <div style="display:grid; gap:20px;">
                <div class="cmp-detail-panel">
                    <div class="cmp-panel-head">
                        <h2 class="cmp-panel-title">{{ $isArabic ? 'تفاصيل السعر المختار' : 'Selected price details' }}</h2>
                    </div>

                    @if ($selectedPrice)
                        @php
                            $cityName = $isArabic
                                ? ($selectedPrice->city->name_ar ?? $selectedPrice->city->name_en ?? '—')
                                : ($selectedPrice->city->name_en ?? $selectedPrice->city->name_ar ?? '—');

                            $materialName = $isArabic
                                ? ($selectedPrice->materialType->name_ar ?? $selectedPrice->materialType->name_en ?? '—')
                                : ($selectedPrice->materialType->name_en ?? $selectedPrice->materialType->name_ar ?? '—');
                        @endphp

                        <h3 class="cmp-detail-title">{{ $materialName }}</h3>
                        <div class="cmp-detail-sub">{{ $cityName }}</div>

                        <div class="cmp-detail-grid">
                            <div class="cmp-detail-box">
                                <span>{{ $isArabic ? 'السعر' : 'Price' }}</span>
                                <strong>{{ number_format((float) $selectedPrice->price, 2) }}</strong>
                            </div>

                            <div class="cmp-detail-box">
                                <span>{{ $isArabic ? 'العملة' : 'Currency' }}</span>
                                <strong>{{ $selectedPrice->currency ?? '—' }}</strong>
                            </div>

                            <div class="cmp-detail-box">
                                <span>{{ $isArabic ? 'الحالة' : 'Status' }}</span>
                                <strong>
                                    {{ $selectedPrice->is_active
                                        ? ($isArabic ? 'مفعل' : 'Active')
                                        : ($isArabic ? 'غير مفعل' : 'Inactive') }}
                                </strong>
                            </div>

                            <div class="cmp-detail-box">
                                <span>{{ $isArabic ? 'الوحدة' : 'Unit' }}</span>
                                <strong>{{ $selectedPrice->materialType->base_unit ?? '—' }}</strong>
                            </div>

                            <div class="cmp-detail-box">
                                <span>{{ $isArabic ? 'كود المادة' : 'Material code' }}</span>
                                <strong>{{ $selectedPrice->materialType->code ?? '—' }}</strong>
                            </div>

                            <div class="cmp-detail-box">
                                <span>{{ $isArabic ? 'تاريخ السريان' : 'Effective from' }}</span>
                                <strong>{{ optional($selectedPrice->effective_from)->format('Y-m-d') ?? '—' }}</strong>
                            </div>
                        </div>
                    @else
                        <div class="cmp-empty">
                            {{ $isArabic ? 'اختر سعرًا من القائمة لعرض التفاصيل.' : 'Select a price from the list to view details.' }}
                        </div>
                    @endif
                </div>

                <div class="cmp-form-panel">
                    <div class="cmp-panel-head">
                        <h2 class="cmp-panel-title">
                            {{ $selectedPrice
                                ? ($isArabic ? 'تعديل سعر المادة' : 'Edit material price')
                                : ($isArabic ? 'إضافة سعر جديد' : 'Create new price') }}
                        </h2>
                    </div>

                    <form
                        method="POST"
                        action="{{ $selectedPrice ? route('admin.city-material-prices.update', $selectedPrice->id) : route('admin.city-material-prices.store') }}"
                        class="cmp-form"
                    >
                        @csrf
                        @if ($selectedPrice)
                            @method('PUT')
                        @endif

                        <div class="cmp-form-grid">
                            <div class="cmp-group">
                                <label class="cmp-label">{{ $isArabic ? 'المدينة' : 'City' }}</label>
                                <select name="city_id" class="cmp-select">
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}"
                                            @selected(old('city_id', $selectedPrice->city_id ?? null) == $city->id)>
                                            {{ $isArabic
                                                ? ($city->name_ar ?? $city->name_en ?? '—')
                                                : ($city->name_en ?? $city->name_ar ?? '—') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('city_id') <div class="cmp-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="cmp-group">
                                <label class="cmp-label">{{ $isArabic ? 'المادة' : 'Material' }}</label>
                                <select name="material_type_id" class="cmp-select">
                                    @foreach ($materialTypes as $materialType)
                                        <option value="{{ $materialType->id }}"
                                            @selected(old('material_type_id', $selectedPrice->material_type_id ?? null) == $materialType->id)>
                                            {{ $isArabic
                                                ? ($materialType->name_ar ?? $materialType->name_en ?? '—')
                                                : ($materialType->name_en ?? $materialType->name_ar ?? '—') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('material_type_id') <div class="cmp-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="cmp-group">
                                <label class="cmp-label">{{ $isArabic ? 'السعر' : 'Price' }}</label>
                                <input type="number" step="0.01" name="price" class="cmp-input"
                                       value="{{ old('price', $selectedPrice->price ?? '') }}">
                                @error('price') <div class="cmp-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="cmp-group">
                                <label class="cmp-label">{{ $isArabic ? 'العملة' : 'Currency' }}</label>
                                <input type="text" name="currency" class="cmp-input"
                                       value="{{ old('currency', $selectedPrice->currency ?? 'SYP') }}">
                                @error('currency') <div class="cmp-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="cmp-group">
                                <label class="cmp-label">{{ $isArabic ? 'تاريخ السريان' : 'Effective from' }}</label>
                                <input type="date" name="effective_from" class="cmp-input"
                                       value="{{ old('effective_from', optional($selectedPrice?->effective_from)->format('Y-m-d') ?? now()->toDateString()) }}">
                                @error('effective_from') <div class="cmp-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="cmp-group">
                                <label class="cmp-label">{{ $isArabic ? 'الحالة' : 'Status' }}</label>
                                <select name="is_active" class="cmp-select">
                                    <option value="1" @selected(old('is_active', $selectedPrice->is_active ?? true) == 1)>
                                        {{ $isArabic ? 'مفعل' : 'Active' }}
                                    </option>
                                    <option value="0" @selected(old('is_active', $selectedPrice->is_active ?? true) == 0)>
                                        {{ $isArabic ? 'غير مفعل' : 'Inactive' }}
                                    </option>
                                </select>
                                @error('is_active') <div class="cmp-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="cmp-actions">
                            <button type="submit" class="cmp-btn-primary">
                                {{ $selectedPrice
                                    ? ($isArabic ? 'حفظ التعديلات' : 'Save changes')
                                    : ($isArabic ? 'إضافة السعر' : 'Create price') }}
                            </button>

                            @if ($selectedPrice)
                                <a href="{{ route('admin.city-material-prices.index') }}" class="cmp-btn-secondary">
                                    {{ $isArabic ? 'عنصر جديد' : 'New item' }}
                                </a>
                            @endif
                        </div>
                    </form>

                    @if ($selectedPrice)
                        <form method="POST" action="{{ route('admin.city-material-prices.destroy', $selectedPrice->id) }}" style="margin-top:14px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="cmp-btn-danger">
                                {{ $isArabic ? 'حذف السعر' : 'Delete price' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection