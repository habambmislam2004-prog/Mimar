@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $selectedCategory = $selectedCategory ?? null;

        $allCategories = $categories->total();
        $activeCategories = collect($categories->items())->where('is_active', true)->count();
        $inactiveCategories = collect($categories->items())->where('is_active', false)->count();
        $selectedSubcategoriesCount = $selectedCategory && isset($selectedCategory->subcategories)
            ? $selectedCategory->subcategories->count()
            : 0;
    @endphp

    <style>
        .category-admin-shell { display: grid; gap: 24px; }

        .category-admin-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 34px;
            background: linear-gradient(135deg, #0f172a 0%, #172554 48%, #1e3a8a 100%);
            color: #fff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
        }

        .category-admin-hero::before {
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

        .category-admin-hero::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            top: -110px;
            inset-inline-end: -80px;
            background: radial-gradient(circle, rgba(255,255,255,.16), transparent 66%);
        }

        .category-admin-hero-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 24px;
            align-items: end;
        }

        .category-admin-kicker {
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

        .category-admin-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #eab308;
        }

        .category-admin-title {
            margin: 0 0 12px;
            font-size: 44px;
            line-height: 1.02;
            letter-spacing: -0.05em;
            font-weight: 900;
        }

        .category-admin-copy {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .category-admin-hero-side {
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 28px;
            padding: 20px;
            backdrop-filter: blur(12px);
        }

        .category-admin-hero-side h3 {
            margin: 0 0 14px;
            font-size: 19px;
            font-weight: 800;
        }

        .category-admin-hero-list {
            display: grid;
            gap: 12px;
        }

        .category-admin-hero-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,.86);
            font-size: 14px;
        }

        .category-admin-hero-item strong {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
        }

        .category-admin-alert {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(5,150,105,.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,.12);
            font-size: 14px;
            font-weight: 700;
        }

        .category-admin-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .category-stat-card {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 26px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
        }

        .category-stat-label {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .category-stat-number {
            font-size: 36px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
            margin-bottom: 6px;
        }

        .category-stat-note {
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.8;
        }

        .category-admin-layout {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 20px;
            align-items: start;
        }

        .category-panel,
        .category-detail-panel,
        .category-form-panel {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 30px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
            padding: 24px;
        }

        .category-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .category-panel-title {
            margin: 0;
            font-size: 25px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.03em;
        }

        .category-panel-sub {
            color: #64748b;
            font-size: 13px;
        }

        .category-cards-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .category-card {
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

        .category-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 34px rgba(15,23,42,.08);
        }

        .category-card.active {
            border-color: rgba(59,130,246,.28);
            box-shadow: 0 18px 34px rgba(59,130,246,.10);
            background: linear-gradient(180deg, rgba(239,246,255,1) 0%, rgba(248,250,252,1) 100%);
        }

        .category-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .category-card-name {
            margin: 0 0 4px;
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.15;
        }

        .category-card-sub {
            color: #64748b;
            font-size: 13px;
        }

        .category-badge {
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

        .category-badge.active {
            background: rgba(5,150,105,.10);
            color: #059669;
        }

        .category-badge.inactive {
            background: rgba(239,68,68,.10);
            color: #dc2626;
        }

        .category-card-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .category-mini-box {
            padding: 12px;
            border-radius: 16px;
            background: rgba(255,255,255,.90);
            border: 1px solid rgba(15,23,42,.06);
        }

        .category-mini-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
        }

        .category-mini-box strong {
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
        }

        .category-pagination {
            margin-top: 18px;
        }

        .category-detail-title {
            margin: 0 0 6px;
            font-size: 32px;
            line-height: 1.05;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
        }

        .category-detail-sub {
            color: #64748b;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .category-detail-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .category-detail-box {
            padding: 14px;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
        }

        .category-detail-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .category-detail-box strong {
            display: block;
            color: #0f172a;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.7;
        }

        .category-sub-wrap {
            margin-top: 20px;
        }

        .category-sub-wrap h3 {
            margin: 0 0 12px;
            font-size: 19px;
            font-weight: 900;
            color: #0f172a;
        }

        .category-sub-list {
            display: grid;
            gap: 12px;
        }

        .category-sub-item {
            padding: 16px;
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
        }

        .category-sub-item strong {
            display: block;
            margin-bottom: 6px;
            color: #0f172a;
            font-size: 16px;
            font-weight: 900;
        }

        .category-sub-meta {
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }

        .category-form {
            display: grid;
            gap: 16px;
        }

        .category-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .category-group {
            display: grid;
            gap: 8px;
        }

        .category-label {
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .category-input,
        .category-select {
            width: 100%;
            border: 1px solid rgba(15,23,42,.08);
            background: #fff;
            border-radius: 18px;
            padding: 13px 14px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .category-input:focus,
        .category-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.10);
        }

        .category-error {
            color: #dc2626;
            font-size: 12px;
        }

        .category-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .category-btn-primary,
        .category-btn-secondary,
        .category-btn-danger {
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

        .category-btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 14px 28px rgba(37,99,235,.18);
        }

        .category-btn-secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,.08);
        }

        .category-btn-danger {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.12);
        }

        .category-empty {
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
            .category-admin-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .category-admin-hero-content,
            .category-admin-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .category-cards-grid,
            .category-detail-grid,
            .category-form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .category-admin-hero,
            .category-panel,
            .category-detail-panel,
            .category-form-panel {
                padding: 20px;
                border-radius: 24px;
            }

            .category-admin-title {
                font-size: 32px;
            }
        }
    </style>

    <div class="category-admin-shell">
        <section class="category-admin-hero">
            <div class="category-admin-hero-content">
                <div>
                    <span class="category-admin-kicker">{{ $isArabic ? 'إدارة التصنيفات' : 'Manage Categories' }}</span>

                    <h1 class="category-admin-title">
                        {{ $isArabic ? 'واجهة احترافية لإدارة التصنيفات الرئيسية' : 'A premium interface for managing main categories' }}
                    </h1>

                    <p class="category-admin-copy">
                        {{ $isArabic
                            ? 'استعرض التصنيفات الرئيسية، عدلها، أضف تصنيفات جديدة، وتابع التصنيفات الفرعية المرتبطة بها ضمن واجهة إدارية أوضح وأكثر فخامة.'
                            : 'Review main categories, edit them, add new ones, and track related subcategories through a cleaner premium admin experience.' }}
                    </p>
                </div>

                <div class="category-admin-hero-side">
                    <h3>{{ $isArabic ? 'ملخص مباشر' : 'Direct summary' }}</h3>

                    <div class="category-admin-hero-list">
                        <div class="category-admin-hero-item">
                            <span>{{ $isArabic ? 'إجمالي التصنيفات' : 'Total categories' }}</span>
                            <strong>{{ $categories->total() }}</strong>
                        </div>

                        <div class="category-admin-hero-item">
                            <span>{{ $isArabic ? 'المعروضة الآن' : 'Shown now' }}</span>
                            <strong>{{ $categories->count() }}</strong>
                        </div>

                        <div class="category-admin-hero-item">
                            <span>{{ $isArabic ? 'الصفحة الحالية' : 'Current page' }}</span>
                            <strong>{{ $categories->currentPage() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="category-admin-alert">{{ session('success') }}</div>
        @endif

        <section class="category-admin-stats">
            <div class="category-stat-card">
                <span class="category-stat-label">{{ $isArabic ? 'إجمالي التصنيفات' : 'Total categories' }}</span>
                <div class="category-stat-number">{{ $allCategories }}</div>
                <div class="category-stat-note">{{ $isArabic ? 'كل التصنيفات المسجلة في النظام' : 'All categories recorded in the system' }}</div>
            </div>

            <div class="category-stat-card">
                <span class="category-stat-label">{{ $isArabic ? 'التصنيفات المفعلة' : 'Active categories' }}</span>
                <div class="category-stat-number">{{ $activeCategories }}</div>
                <div class="category-stat-note">{{ $isArabic ? 'التصنيفات المتاحة داخل النظام' : 'Available categories in the system' }}</div>
            </div>

            <div class="category-stat-card">
                <span class="category-stat-label">{{ $isArabic ? 'التصنيفات غير المفعلة' : 'Inactive categories' }}</span>
                <div class="category-stat-number">{{ $inactiveCategories }}</div>
                <div class="category-stat-note">{{ $isArabic ? 'التصنيفات المخفية أو الموقوفة' : 'Hidden or disabled categories' }}</div>
            </div>

            <div class="category-stat-card">
                <span class="category-stat-label">{{ $isArabic ? 'فروع التصنيف المختار' : 'Selected category branches' }}</span>
                <div class="category-stat-number">{{ $selectedSubcategoriesCount }}</div>
                <div class="category-stat-note">{{ $isArabic ? 'عدد التصنيفات الفرعية المرتبطة' : 'Linked subcategories count' }}</div>
            </div>
        </section>

        <section class="category-admin-layout">
            <div class="category-panel">
                <div class="category-panel-head">
                    <h2 class="category-panel-title">{{ $isArabic ? 'التصنيفات الرئيسية' : 'Main categories' }}</h2>
                    <span class="category-panel-sub">{{ $categories->total() }}</span>
                </div>

                @if ($categories->count())
                    <div class="category-cards-grid">
                        @foreach ($categories as $category)
                            <a href="{{ route('admin.categories.index', ['selected' => $category->id]) }}"
                               class="category-card {{ $selectedCategory && $selectedCategory->id === $category->id ? 'active' : '' }}">
                                <div class="category-card-top">
                                    <div>
                                        <h3 class="category-card-name">{{ $isArabic ? $category->name_ar : $category->name_en }}</h3>
                                        <div class="category-card-sub">{{ $isArabic ? $category->name_en : $category->name_ar }}</div>
                                    </div>

                                    <span class="category-badge {{ $category->is_active ? 'active' : 'inactive' }}">
                                        {{ $category->is_active
                                            ? ($isArabic ? 'مفعل' : 'Active')
                                            : ($isArabic ? 'غير مفعل' : 'Inactive') }}
                                    </span>
                                </div>

                                <div class="category-card-grid">
                                    <div class="category-mini-box">
                                        <span>{{ $isArabic ? 'الترتيب' : 'Sort order' }}</span>
                                        <strong>{{ $category->sort_order }}</strong>
                                    </div>

                                    <div class="category-mini-box">
                                        <span>{{ $isArabic ? 'الأيقونة' : 'Icon' }}</span>
                                        <strong>{{ $category->icon ?: '—' }}</strong>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="category-pagination">
                        {{ $categories->withQueryString()->links() }}
                    </div>
                @else
                    <div class="category-empty">
                        {{ $isArabic ? 'لا توجد تصنيفات حالياً.' : 'There are no categories yet.' }}
                    </div>
                @endif
            </div>

            <div style="display:grid; gap:20px;">
                <div class="category-detail-panel">
                    <div class="category-panel-head">
                        <h2 class="category-panel-title">{{ $isArabic ? 'تفاصيل التصنيف المختار' : 'Selected category details' }}</h2>
                    </div>

                    @if ($selectedCategory)
                        <h3 class="category-detail-title">{{ $isArabic ? $selectedCategory->name_ar : $selectedCategory->name_en }}</h3>
                        <div class="category-detail-sub">{{ $isArabic ? $selectedCategory->name_en : $selectedCategory->name_ar }}</div>

                        <div class="category-detail-grid">
                            <div class="category-detail-box">
                                <span>{{ $isArabic ? 'الترتيب' : 'Sort order' }}</span>
                                <strong>{{ $selectedCategory->sort_order }}</strong>
                            </div>

                            <div class="category-detail-box">
                                <span>{{ $isArabic ? 'الحالة' : 'Status' }}</span>
                                <strong>
                                    {{ $selectedCategory->is_active
                                        ? ($isArabic ? 'مفعل' : 'Active')
                                        : ($isArabic ? 'غير مفعل' : 'Inactive') }}
                                </strong>
                            </div>

                            <div class="category-detail-box">
                                <span>{{ $isArabic ? 'الأيقونة' : 'Icon' }}</span>
                                <strong>{{ $selectedCategory->icon ?: '—' }}</strong>
                            </div>
                        </div>

                        <div class="category-sub-wrap">
                            <h3>{{ $isArabic ? 'التصنيفات الفرعية المرتبطة' : 'Linked subcategories' }}</h3>

                            @if (isset($selectedCategory->subcategories) && $selectedCategory->subcategories->count())
                                <div class="category-sub-list">
                                    @foreach ($selectedCategory->subcategories as $subcategory)
                                        <div class="category-sub-item">
                                            <strong>{{ $isArabic ? $subcategory->name_ar : $subcategory->name_en }}</strong>
                                            <div class="category-sub-meta">
                                                {{ $isArabic ? 'الاسم المقابل:' : 'Alternative name:' }}
                                                {{ $isArabic ? $subcategory->name_en : $subcategory->name_ar }}
                                            </div>
                                            <div class="category-sub-meta">
                                                {{ $isArabic ? 'الترتيب:' : 'Sort order:' }} {{ $subcategory->sort_order }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="category-empty" style="margin-top:12px;">
                                    {{ $isArabic ? 'لا توجد تصنيفات فرعية مرتبطة بهذا التصنيف حالياً.' : 'There are no subcategories linked to this category yet.' }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="category-empty">
                            {{ $isArabic ? 'اختر تصنيفاً لعرض التفاصيل.' : 'Select a category to view details.' }}
                        </div>
                    @endif
                </div>

                <div class="category-form-panel">
                    <div class="category-panel-head">
                        <h2 class="category-panel-title">
                            {{ $selectedCategory
                                ? ($isArabic ? 'تعديل التصنيف' : 'Edit category')
                                : ($isArabic ? 'إضافة تصنيف' : 'Create category') }}
                        </h2>
                    </div>

                    <form
                        method="POST"
                        action="{{ $selectedCategory ? route('admin.categories.update', $selectedCategory->id) : route('admin.categories.store') }}"
                        class="category-form"
                    >
                        @csrf
                        @if ($selectedCategory)
                            @method('PUT')
                        @endif

                        <div class="category-form-grid">
                            <div class="category-group">
                                <label class="category-label">{{ $isArabic ? 'الاسم بالعربية' : 'Arabic name' }}</label>
                                <input type="text" name="name_ar" class="category-input" value="{{ old('name_ar', $selectedCategory->name_ar ?? '') }}">
                                @error('name_ar') <div class="category-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="category-group">
                                <label class="category-label">{{ $isArabic ? 'الاسم بالإنجليزية' : 'English name' }}</label>
                                <input type="text" name="name_en" class="category-input" value="{{ old('name_en', $selectedCategory->name_en ?? '') }}">
                                @error('name_en') <div class="category-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="category-group">
                                <label class="category-label">{{ $isArabic ? 'الأيقونة' : 'Icon' }}</label>
                                <input type="text" name="icon" class="category-input" value="{{ old('icon', $selectedCategory->icon ?? '') }}">
                                @error('icon') <div class="category-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="category-group">
                                <label class="category-label">{{ $isArabic ? 'الترتيب' : 'Sort order' }}</label>
                                <input type="number" name="sort_order" class="category-input" value="{{ old('sort_order', $selectedCategory->sort_order ?? 0) }}">
                                @error('sort_order') <div class="category-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="category-group">
                                <label class="category-label">{{ $isArabic ? 'الحالة' : 'Status' }}</label>
                                <select name="is_active" class="category-select">
                                    <option value="1" @selected(old('is_active', $selectedCategory->is_active ?? 1) == 1)>{{ $isArabic ? 'مفعل' : 'Active' }}</option>
                                    <option value="0" @selected(old('is_active', $selectedCategory->is_active ?? 1) == 0)>{{ $isArabic ? 'غير مفعل' : 'Inactive' }}</option>
                                </select>
                                @error('is_active') <div class="category-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="category-actions">
                            <button type="submit" class="category-btn-primary">
                                {{ $selectedCategory
                                    ? ($isArabic ? 'حفظ التعديلات' : 'Save changes')
                                    : ($isArabic ? 'إضافة التصنيف' : 'Create category') }}
                            </button>

                            @if ($selectedCategory)
                                <a href="{{ route('admin.categories.index') }}" class="category-btn-secondary">
                                    {{ $isArabic ? 'تصنيف جديد' : 'New category' }}
                                </a>
                            @endif
                        </div>
                    </form>

                    @if ($selectedCategory)
                        <form method="POST" action="{{ route('admin.categories.destroy', $selectedCategory->id) }}" style="margin-top:14px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="category-btn-danger">
                                {{ $isArabic ? 'حذف التصنيف' : 'Delete category' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection