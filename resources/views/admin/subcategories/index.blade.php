@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $selectedSubcategory = $selectedSubcategory ?? null;

        $allSubcategories = $subcategories->total();
        $activeSubcategories = collect($subcategories->items())->where('is_active', true)->count();
        $inactiveSubcategories = collect($subcategories->items())->where('is_active', false)->count();
    @endphp

    <style>
        .subcategory-admin-shell { display: grid; gap: 24px; }

        .subcategory-admin-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 34px;
            background: linear-gradient(135deg, #0f172a 0%, #172554 48%, #1e3a8a 100%);
            color: #fff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
        }

        .subcategory-admin-hero::before {
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

        .subcategory-admin-hero::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            top: -110px;
            inset-inline-end: -80px;
            background: radial-gradient(circle, rgba(255,255,255,.16), transparent 66%);
        }

        .subcategory-admin-hero-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 24px;
            align-items: end;
        }

        .subcategory-admin-kicker {
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

        .subcategory-admin-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #eab308;
        }

        .subcategory-admin-title {
            margin: 0 0 12px;
            font-size: 44px;
            line-height: 1.02;
            letter-spacing: -0.05em;
            font-weight: 900;
        }

        .subcategory-admin-copy {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .subcategory-admin-hero-side {
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 28px;
            padding: 20px;
            backdrop-filter: blur(12px);
        }

        .subcategory-admin-hero-side h3 {
            margin: 0 0 14px;
            font-size: 19px;
            font-weight: 800;
        }

        .subcategory-admin-hero-list {
            display: grid;
            gap: 12px;
        }

        .subcategory-admin-hero-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,.86);
            font-size: 14px;
        }

        .subcategory-admin-hero-item strong {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
        }

        .subcategory-admin-alert {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(5,150,105,.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,.12);
            font-size: 14px;
            font-weight: 700;
        }

        .subcategory-admin-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .subcategory-stat-card {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 26px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
        }

        .subcategory-stat-label {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .subcategory-stat-number {
            font-size: 36px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
            margin-bottom: 6px;
        }

        .subcategory-stat-note {
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.8;
        }

        .subcategory-admin-layout {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 20px;
            align-items: start;
        }

        .subcategory-panel,
        .subcategory-detail-panel,
        .subcategory-form-panel {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 30px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
            padding: 24px;
        }

        .subcategory-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .subcategory-panel-title {
            margin: 0;
            font-size: 25px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.03em;
        }

        .subcategory-panel-sub {
            color: #64748b;
            font-size: 13px;
        }

        .subcategory-cards-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .subcategory-card {
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

        .subcategory-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 34px rgba(15,23,42,.08);
        }

        .subcategory-card.active {
            border-color: rgba(59,130,246,.28);
            box-shadow: 0 18px 34px rgba(59,130,246,.10);
            background: linear-gradient(180deg, rgba(239,246,255,1) 0%, rgba(248,250,252,1) 100%);
        }

        .subcategory-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .subcategory-card-name {
            margin: 0 0 4px;
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.15;
        }

        .subcategory-card-sub {
            color: #64748b;
            font-size: 13px;
        }

        .subcategory-badge {
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

        .subcategory-badge.active {
            background: rgba(5,150,105,.10);
            color: #059669;
        }

        .subcategory-badge.inactive {
            background: rgba(239,68,68,.10);
            color: #dc2626;
        }

        .subcategory-card-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .subcategory-mini-box {
            padding: 12px;
            border-radius: 16px;
            background: rgba(255,255,255,.90);
            border: 1px solid rgba(15,23,42,.06);
        }

        .subcategory-mini-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
        }

        .subcategory-mini-box strong {
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
        }

        .subcategory-pagination {
            margin-top: 18px;
        }

        .subcategory-detail-title {
            margin: 0 0 6px;
            font-size: 32px;
            line-height: 1.05;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
        }

        .subcategory-detail-sub {
            color: #64748b;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .subcategory-detail-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .subcategory-detail-box {
            padding: 14px;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
        }

        .subcategory-detail-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .subcategory-detail-box strong {
            display: block;
            color: #0f172a;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.7;
        }

        .subcategory-form {
            display: grid;
            gap: 16px;
        }

        .subcategory-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .subcategory-group {
            display: grid;
            gap: 8px;
        }

        .subcategory-label {
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .subcategory-input,
        .subcategory-select {
            width: 100%;
            border: 1px solid rgba(15,23,42,.08);
            background: #fff;
            border-radius: 18px;
            padding: 13px 14px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .subcategory-input:focus,
        .subcategory-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.10);
        }

        .subcategory-error {
            color: #dc2626;
            font-size: 12px;
        }

        .subcategory-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .subcategory-btn-primary,
        .subcategory-btn-secondary,
        .subcategory-btn-danger {
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

        .subcategory-btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 14px 28px rgba(37,99,235,.18);
        }

        .subcategory-btn-secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,.08);
        }

        .subcategory-btn-danger {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.12);
        }

        .subcategory-empty {
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
            .subcategory-admin-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .subcategory-admin-hero-content,
            .subcategory-admin-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .subcategory-cards-grid,
            .subcategory-detail-grid,
            .subcategory-form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .subcategory-admin-hero,
            .subcategory-panel,
            .subcategory-detail-panel,
            .subcategory-form-panel {
                padding: 20px;
                border-radius: 24px;
            }

            .subcategory-admin-title {
                font-size: 32px;
            }
        }
    </style>

    <div class="subcategory-admin-shell">
        <section class="subcategory-admin-hero">
            <div class="subcategory-admin-hero-content">
                <div>
                    <span class="subcategory-admin-kicker">{{ $isArabic ? 'إدارة التصنيفات الفرعية' : 'Manage Subcategories' }}</span>

                    <h1 class="subcategory-admin-title">
                        {{ $isArabic ? 'واجهة احترافية لإدارة التصنيفات الفرعية' : 'A premium interface for managing subcategories' }}
                    </h1>

                    <p class="subcategory-admin-copy">
                        {{ $isArabic
                            ? 'استعرض التصنيفات الفرعية، اربطها بالتصنيفات الرئيسية، وعدل بياناتها ضمن واجهة إدارية أوضح وأكثر فخامة.'
                            : 'Review subcategories, link them to main categories, and edit their information through a cleaner premium admin experience.' }}
                    </p>
                </div>

                <div class="subcategory-admin-hero-side">
                    <h3>{{ $isArabic ? 'ملخص مباشر' : 'Direct summary' }}</h3>

                    <div class="subcategory-admin-hero-list">
                        <div class="subcategory-admin-hero-item">
                            <span>{{ $isArabic ? 'إجمالي التصنيفات الفرعية' : 'Total subcategories' }}</span>
                            <strong>{{ $subcategories->total() }}</strong>
                        </div>

                        <div class="subcategory-admin-hero-item">
                            <span>{{ $isArabic ? 'المعروضة الآن' : 'Shown now' }}</span>
                            <strong>{{ $subcategories->count() }}</strong>
                        </div>

                        <div class="subcategory-admin-hero-item">
                            <span>{{ $isArabic ? 'الصفحة الحالية' : 'Current page' }}</span>
                            <strong>{{ $subcategories->currentPage() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="subcategory-admin-alert">{{ session('success') }}</div>
        @endif

        <section class="subcategory-admin-stats">
            <div class="subcategory-stat-card">
                <span class="subcategory-stat-label">{{ $isArabic ? 'إجمالي التصنيفات الفرعية' : 'Total subcategories' }}</span>
                <div class="subcategory-stat-number">{{ $allSubcategories }}</div>
                <div class="subcategory-stat-note">{{ $isArabic ? 'كل التصنيفات الفرعية المسجلة' : 'All subcategories recorded in the system' }}</div>
            </div>

            <div class="subcategory-stat-card">
                <span class="subcategory-stat-label">{{ $isArabic ? 'المفعلة' : 'Active' }}</span>
                <div class="subcategory-stat-number">{{ $activeSubcategories }}</div>
                <div class="subcategory-stat-note">{{ $isArabic ? 'التصنيفات الفرعية المتاحة' : 'Available subcategories' }}</div>
            </div>

            <div class="subcategory-stat-card">
                <span class="subcategory-stat-label">{{ $isArabic ? 'غير المفعلة' : 'Inactive' }}</span>
                <div class="subcategory-stat-number">{{ $inactiveSubcategories }}</div>
                <div class="subcategory-stat-note">{{ $isArabic ? 'المخفية أو الموقوفة' : 'Hidden or disabled items' }}</div>
            </div>

            <div class="subcategory-stat-card">
                <span class="subcategory-stat-label">{{ $isArabic ? 'عدد التصنيفات الرئيسية' : 'Main categories count' }}</span>
                <div class="subcategory-stat-number">{{ $categories->count() }}</div>
                <div class="subcategory-stat-note">{{ $isArabic ? 'المتاحة للربط' : 'Available for linking' }}</div>
            </div>
        </section>

        <section class="subcategory-admin-layout">
            <div class="subcategory-panel">
                <div class="subcategory-panel-head">
                    <h2 class="subcategory-panel-title">{{ $isArabic ? 'التصنيفات الفرعية' : 'Subcategories' }}</h2>
                    <span class="subcategory-panel-sub">{{ $subcategories->total() }}</span>
                </div>

                @if ($subcategories->count())
                    <div class="subcategory-cards-grid">
                        @foreach ($subcategories as $subcategory)
                            <a href="{{ route('admin.subcategories.index', ['selected' => $subcategory->id]) }}"
                               class="subcategory-card {{ $selectedSubcategory && $selectedSubcategory->id === $subcategory->id ? 'active' : '' }}">
                                <div class="subcategory-card-top">
                                    <div>
                                        <h3 class="subcategory-card-name">{{ $isArabic ? $subcategory->name_ar : $subcategory->name_en }}</h3>
                                        <div class="subcategory-card-sub">{{ $isArabic ? $subcategory->name_en : $subcategory->name_ar }}</div>
                                    </div>

                                    <span class="subcategory-badge {{ $subcategory->is_active ? 'active' : 'inactive' }}">
                                        {{ $subcategory->is_active
                                            ? ($isArabic ? 'مفعل' : 'Active')
                                            : ($isArabic ? 'غير مفعل' : 'Inactive') }}
                                    </span>
                                </div>

                                <div class="subcategory-card-grid">
                                    <div class="subcategory-mini-box">
                                        <span>{{ $isArabic ? 'الترتيب' : 'Sort order' }}</span>
                                        <strong>{{ $subcategory->sort_order }}</strong>
                                    </div>

                                    <div class="subcategory-mini-box">
                                        <span>{{ $isArabic ? 'التصنيف الرئيسي' : 'Main category' }}</span>
                                        <strong>
                                            {{ $isArabic
                                                ? ($subcategory->category->name_ar ?? '—')
                                                : ($subcategory->category->name_en ?? '—') }}
                                        </strong>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="subcategory-pagination">
                        {{ $subcategories->withQueryString()->links() }}
                    </div>
                @else
                    <div class="subcategory-empty">
                        {{ $isArabic ? 'لا توجد تصنيفات فرعية حالياً.' : 'There are no subcategories yet.' }}
                    </div>
                @endif
            </div>

            <div style="display:grid; gap:20px;">
                <div class="subcategory-detail-panel">
                    <div class="subcategory-panel-head">
                        <h2 class="subcategory-panel-title">{{ $isArabic ? 'تفاصيل التصنيف الفرعي المختار' : 'Selected subcategory details' }}</h2>
                    </div>

                    @if ($selectedSubcategory)
                        <h3 class="subcategory-detail-title">{{ $isArabic ? $selectedSubcategory->name_ar : $selectedSubcategory->name_en }}</h3>
                        <div class="subcategory-detail-sub">{{ $isArabic ? $selectedSubcategory->name_en : $selectedSubcategory->name_ar }}</div>

                        <div class="subcategory-detail-grid">
                            <div class="subcategory-detail-box">
                                <span>{{ $isArabic ? 'التصنيف الرئيسي' : 'Main category' }}</span>
                                <strong>
                                    {{ $isArabic
                                        ? ($selectedSubcategory->category->name_ar ?? '—')
                                        : ($selectedSubcategory->category->name_en ?? '—') }}
                                </strong>
                            </div>

                            <div class="subcategory-detail-box">
                                <span>{{ $isArabic ? 'الترتيب' : 'Sort order' }}</span>
                                <strong>{{ $selectedSubcategory->sort_order }}</strong>
                            </div>

                            <div class="subcategory-detail-box">
                                <span>{{ $isArabic ? 'الحالة' : 'Status' }}</span>
                                <strong>
                                    {{ $selectedSubcategory->is_active
                                        ? ($isArabic ? 'مفعل' : 'Active')
                                        : ($isArabic ? 'غير مفعل' : 'Inactive') }}
                                </strong>
                            </div>
                        </div>
                    @else
                        <div class="subcategory-empty">
                            {{ $isArabic ? 'اختر تصنيفاً فرعياً لعرض التفاصيل.' : 'Select a subcategory to view details.' }}
                        </div>
                    @endif
                </div>

                <div class="subcategory-form-panel">
                    <div class="subcategory-panel-head">
                        <h2 class="subcategory-panel-title">
                            {{ $selectedSubcategory
                                ? ($isArabic ? 'تعديل التصنيف الفرعي' : 'Edit subcategory')
                                : ($isArabic ? 'إضافة تصنيف فرعي' : 'Create subcategory') }}
                        </h2>
                    </div>

                    <form
                        method="POST"
                        action="{{ $selectedSubcategory ? route('admin.subcategories.update', $selectedSubcategory->id) : route('admin.subcategories.store') }}"
                        class="subcategory-form"
                    >
                        @csrf
                        @if ($selectedSubcategory)
                            @method('PUT')
                        @endif

                        <div class="subcategory-form-grid">
                            <div class="subcategory-group">
                                <label class="subcategory-label">{{ $isArabic ? 'التصنيف الرئيسي' : 'Main category' }}</label>
                                <select name="category_id" class="subcategory-select">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            @selected(old('category_id', $selectedSubcategory->category_id ?? null) == $category->id)>
                                            {{ $isArabic ? $category->name_ar : $category->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="subcategory-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="subcategory-group">
                                <label class="subcategory-label">{{ $isArabic ? 'الترتيب' : 'Sort order' }}</label>
                                <input type="number" name="sort_order" class="subcategory-input" value="{{ old('sort_order', $selectedSubcategory->sort_order ?? 0) }}">
                                @error('sort_order') <div class="subcategory-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="subcategory-group">
                                <label class="subcategory-label">{{ $isArabic ? 'الاسم بالعربية' : 'Arabic name' }}</label>
                                <input type="text" name="name_ar" class="subcategory-input" value="{{ old('name_ar', $selectedSubcategory->name_ar ?? '') }}">
                                @error('name_ar') <div class="subcategory-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="subcategory-group">
                                <label class="subcategory-label">{{ $isArabic ? 'الاسم بالإنجليزية' : 'English name' }}</label>
                                <input type="text" name="name_en" class="subcategory-input" value="{{ old('name_en', $selectedSubcategory->name_en ?? '') }}">
                                @error('name_en') <div class="subcategory-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="subcategory-group">
                                <label class="subcategory-label">{{ $isArabic ? 'الحالة' : 'Status' }}</label>
                                <select name="is_active" class="subcategory-select">
                                    <option value="1" @selected(old('is_active', $selectedSubcategory->is_active ?? 1) == 1)>{{ $isArabic ? 'مفعل' : 'Active' }}</option>
                                    <option value="0" @selected(old('is_active', $selectedSubcategory->is_active ?? 1) == 0)>{{ $isArabic ? 'غير مفعل' : 'Inactive' }}</option>
                                </select>
                                @error('is_active') <div class="subcategory-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="subcategory-actions">
                            <button type="submit" class="subcategory-btn-primary">
                                {{ $selectedSubcategory
                                    ? ($isArabic ? 'حفظ التعديلات' : 'Save changes')
                                    : ($isArabic ? 'إضافة التصنيف الفرعي' : 'Create subcategory') }}
                            </button>

                            @if ($selectedSubcategory)
                                <a href="{{ route('admin.subcategories.index') }}" class="subcategory-btn-secondary">
                                    {{ $isArabic ? 'تصنيف فرعي جديد' : 'New subcategory' }}
                                </a>
                            @endif
                        </div>
                    </form>

                    @if ($selectedSubcategory)
                        <form method="POST" action="{{ route('admin.subcategories.destroy', $selectedSubcategory->id) }}" style="margin-top:14px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="subcategory-btn-danger">
                                {{ $isArabic ? 'حذف التصنيف الفرعي' : 'Delete subcategory' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection