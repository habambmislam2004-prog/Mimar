@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $selectedField = $selectedField ?? null;

        $allFields = $fields->total();
        $activeFields = $fields->getCollection()->where('is_active', true)->count();
        $inactiveFields = $fields->getCollection()->where('is_active', false)->count();
        $requiredFields = $fields->getCollection()->where('is_required', true)->count();
    @endphp

    <style>
        .dynamic-admin-shell {
            display: grid;
            gap: 24px;
        }

        .dynamic-admin-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 34px;
            background:
                linear-gradient(135deg, #0f172a 0%, #172554 48%, #1e3a8a 100%);
            color: #fff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
        }

        .dynamic-admin-hero::before {
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

        .dynamic-admin-hero::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            top: -110px;
            inset-inline-end: -80px;
            background: radial-gradient(circle, rgba(255,255,255,.16), transparent 66%);
        }

        .dynamic-admin-hero-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 24px;
            align-items: end;
        }

        .dynamic-admin-kicker {
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

        .dynamic-admin-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #eab308;
        }

        .dynamic-admin-title {
            margin: 0 0 12px;
            font-size: 46px;
            line-height: 1.02;
            letter-spacing: -0.05em;
            font-weight: 900;
        }

        .dynamic-admin-copy {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .dynamic-admin-hero-side {
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 28px;
            padding: 20px;
            backdrop-filter: blur(12px);
        }

        .dynamic-admin-hero-side h3 {
            margin: 0 0 14px;
            font-size: 19px;
            font-weight: 800;
        }

        .dynamic-admin-hero-list {
            display: grid;
            gap: 12px;
        }

        .dynamic-admin-hero-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,.86);
            font-size: 14px;
        }

        .dynamic-admin-hero-item strong {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
        }

        .dynamic-admin-alert {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(5,150,105,.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,.12);
            font-size: 14px;
            font-weight: 700;
        }

        .dynamic-admin-errors {
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(239,68,68,.10);
            color: #b91c1c;
            border: 1px solid rgba(239,68,68,.12);
            font-size: 14px;
            font-weight: 700;
        }

        .dynamic-admin-errors ul {
            margin: 0;
            padding-inline-start: 18px;
        }

        .dynamic-admin-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .dynamic-stat-card {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 26px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
        }

        .dynamic-stat-label {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .dynamic-stat-number {
            font-size: 36px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
            margin-bottom: 6px;
        }

        .dynamic-stat-note {
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.8;
        }

        .dynamic-admin-layout {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 20px;
            align-items: start;
        }

        .dynamic-panel,
        .dynamic-detail-panel,
        .dynamic-form-panel,
        .dynamic-filter-panel {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 30px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
            padding: 24px;
        }

        .dynamic-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .dynamic-panel-title {
            margin: 0;
            font-size: 25px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.03em;
        }

        .dynamic-panel-sub {
            color: #64748b;
            font-size: 13px;
        }

        .dynamic-cards-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .dynamic-card {
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

        .dynamic-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 34px rgba(15,23,42,.08);
        }

        .dynamic-card.active {
            border-color: rgba(59,130,246,.28);
            box-shadow: 0 18px 34px rgba(59,130,246,.10);
            background: linear-gradient(180deg, rgba(239,246,255,1) 0%, rgba(248,250,252,1) 100%);
        }

        .dynamic-card.active::after {
            content: "";
            position: absolute;
            inset-inline-end: -20px;
            top: -20px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,.14), transparent 70%);
        }

        .dynamic-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .dynamic-card-name {
            margin: 0 0 4px;
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.15;
        }

        .dynamic-card-sub {
            color: #64748b;
            font-size: 13px;
        }

        .dynamic-badge {
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

        .dynamic-badge.active {
            background: rgba(5,150,105,.10);
            color: #059669;
        }

        .dynamic-badge.inactive {
            background: rgba(239,68,68,.10);
            color: #dc2626;
        }

        .dynamic-card-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .dynamic-mini-box {
            padding: 12px;
            border-radius: 16px;
            background: rgba(255,255,255,.90);
            border: 1px solid rgba(15,23,42,.06);
        }

        .dynamic-mini-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
        }

        .dynamic-mini-box strong {
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
        }

        .dynamic-pagination {
            margin-top: 18px;
        }

        .dynamic-detail-title {
            margin: 0 0 6px;
            font-size: 32px;
            line-height: 1.05;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
        }

        .dynamic-detail-sub {
            color: #64748b;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .dynamic-detail-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .dynamic-detail-box {
            padding: 14px;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
        }

        .dynamic-detail-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .dynamic-detail-box strong {
            display: block;
            color: #0f172a;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.7;
        }

        .dynamic-options-wrap {
            margin-top: 20px;
        }

        .dynamic-options-wrap h3 {
            margin: 0 0 12px;
            font-size: 19px;
            font-weight: 900;
            color: #0f172a;
        }

        .dynamic-options-list {
            display: grid;
            gap: 12px;
        }

        .dynamic-option-item {
            padding: 16px;
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(15,23,42,.06);
            color: #334155;
            font-size: 14px;
            font-weight: 700;
        }

        .dynamic-form {
            display: grid;
            gap: 16px;
        }

        .dynamic-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .dynamic-group {
            display: grid;
            gap: 8px;
        }

        .dynamic-label {
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .dynamic-input,
        .dynamic-select,
        .dynamic-textarea {
            width: 100%;
            border: 1px solid rgba(15,23,42,.08);
            background: #fff;
            border-radius: 18px;
            padding: 13px 14px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .dynamic-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .dynamic-input:focus,
        .dynamic-select:focus,
        .dynamic-textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.10);
        }

        .dynamic-error {
            color: #dc2626;
            font-size: 12px;
        }

        .dynamic-checks {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
            align-items: center;
        }

        .dynamic-check-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #334155;
            font-size: 14px;
            font-weight: 700;
        }

        .dynamic-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .dynamic-btn-primary,
        .dynamic-btn-secondary,
        .dynamic-btn-danger {
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

        .dynamic-btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 14px 28px rgba(37,99,235,.18);
        }

        .dynamic-btn-secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,.08);
        }

        .dynamic-btn-danger {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.12);
        }

        .dynamic-empty {
            padding: 30px;
            border-radius: 22px;
            background: #f8fafc;
            border: 1px dashed rgba(15,23,42,.10);
            color: #64748b;
            font-size: 14px;
            line-height: 1.9;
            text-align: center;
        }

        .dynamic-json-box {
            margin-top: 18px;
            padding: 18px;
            border-radius: 22px;
            background: #0f172a;
            color: #e2e8f0;
            font-size: 13px;
            line-height: 1.9;
            overflow-x: auto;
            direction: ltr;
            text-align: left;
            white-space: pre-wrap;
        }

        .dynamic-filter-help {
            color: #64748b;
            font-size: 13px;
            line-height: 1.9;
            margin-bottom: 18px;
        }

        .dynamic-filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        @media (max-width: 1200px) {
            .dynamic-admin-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dynamic-admin-hero-content,
            .dynamic-admin-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .dynamic-cards-grid,
            .dynamic-detail-grid,
            .dynamic-form-grid,
            .dynamic-filter-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .dynamic-admin-hero,
            .dynamic-panel,
            .dynamic-detail-panel,
            .dynamic-form-panel,
            .dynamic-filter-panel {
                padding: 20px;
                border-radius: 24px;
            }

            .dynamic-admin-title {
                font-size: 32px;
            }
        }
    </style>

    <div class="dynamic-admin-shell">
        <section class="dynamic-admin-hero">
            <div class="dynamic-admin-hero-content">
                <div>
                    <span class="dynamic-admin-kicker">{{ $isArabic ? 'إدارة الحقول الديناميكية' : 'Dynamic Fields Management' }}</span>

                    <h1 class="dynamic-admin-title">
                        {{ $isArabic ? 'واجهة متقدمة لإدارة الحقول الديناميكية وربطها بالتصنيفات' : 'Advanced interface for managing dynamic fields and linking them to categories' }}
                    </h1>

                    <p class="dynamic-admin-copy">
                        {{ $isArabic
                            ? 'من خلال هذه الواجهة يمكنك إضافة الحقول الديناميكية، تنظيمها، ربطها بالتصنيفات والتصنيفات الفرعية، والتحكم بحالتها وخياراتها، ومعاينة الـ JSON والفلترة قبل ربطها بواجهة الخدمات.'
                            : 'This interface lets you create, organize, link, preview JSON output, and prepare filters for dynamic fields before wiring them into the services UI.' }}
                    </p>
                </div>

                <div class="dynamic-admin-hero-side">
                    <h3>{{ $isArabic ? 'ملخص مباشر' : 'Direct summary' }}</h3>

                    <div class="dynamic-admin-hero-list">
                        <div class="dynamic-admin-hero-item">
                            <span>{{ $isArabic ? 'إجمالي الحقول' : 'Total fields' }}</span>
                            <strong>{{ $fields->total() }}</strong>
                        </div>

                        <div class="dynamic-admin-hero-item">
                            <span>{{ $isArabic ? 'المعروضة الآن' : 'Shown now' }}</span>
                            <strong>{{ $fields->count() }}</strong>
                        </div>

                        <div class="dynamic-admin-hero-item">
                            <span>{{ $isArabic ? 'الصفحة الحالية' : 'Current page' }}</span>
                            <strong>{{ $fields->currentPage() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="dynamic-admin-alert">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="dynamic-admin-errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="dynamic-admin-stats">
            <div class="dynamic-stat-card">
                <span class="dynamic-stat-label">{{ $isArabic ? 'إجمالي الحقول' : 'Total fields' }}</span>
                <div class="dynamic-stat-number">{{ $allFields }}</div>
                <div class="dynamic-stat-note">{{ $isArabic ? 'كل الحقول المسجلة في النظام' : 'All registered fields in the system' }}</div>
            </div>

            <div class="dynamic-stat-card">
                <span class="dynamic-stat-label">{{ $isArabic ? 'الحقول المفعلة' : 'Active fields' }}</span>
                <div class="dynamic-stat-number">{{ $activeFields }}</div>
                <div class="dynamic-stat-note">{{ $isArabic ? 'الحقول الجاهزة للاستخدام' : 'Fields ready for use' }}</div>
            </div>

            <div class="dynamic-stat-card">
                <span class="dynamic-stat-label">{{ $isArabic ? 'الحقول غير المفعلة' : 'Inactive fields' }}</span>
                <div class="dynamic-stat-number">{{ $inactiveFields }}</div>
                <div class="dynamic-stat-note">{{ $isArabic ? 'الحقول المخفية أو المعطلة' : 'Hidden or disabled fields' }}</div>
            </div>

            <div class="dynamic-stat-card">
                <span class="dynamic-stat-label">{{ $isArabic ? 'الحقول المطلوبة' : 'Required fields' }}</span>
                <div class="dynamic-stat-number">{{ $requiredFields }}</div>
                <div class="dynamic-stat-note">{{ $isArabic ? 'الحقول الإلزامية داخل النماذج' : 'Mandatory fields in forms' }}</div>
            </div>
        </section>

        <section class="dynamic-admin-layout">
            <div class="dynamic-panel">
                <div class="dynamic-panel-head">
                    <h2 class="dynamic-panel-title">{{ $isArabic ? 'الحقول الديناميكية' : 'Dynamic Fields' }}</h2>
                    <span class="dynamic-panel-sub">{{ $fields->total() }}</span>
                </div>

                @if ($fields->count())
                    <div class="dynamic-cards-grid">
                        @foreach ($fields as $field)
                            <a href="{{ route('admin.dynamic-fields.index', ['selected' => $field->id]) }}"
                               class="dynamic-card {{ $selectedField && $selectedField->id === $field->id ? 'active' : '' }}">
                                <div class="dynamic-card-top">
                                    <div>
                                        <h3 class="dynamic-card-name">{{ $field->label_ar }}</h3>
                                        <div class="dynamic-card-sub">{{ $field->label_en ?: $field->key }}</div>
                                    </div>

                                    <span class="dynamic-badge {{ $field->is_active ? 'active' : 'inactive' }}">
                                        {{ $field->is_active
                                            ? ($isArabic ? 'مفعّل' : 'Active')
                                            : ($isArabic ? 'معطل' : 'Inactive') }}
                                    </span>
                                </div>

                                <div class="dynamic-card-grid">
                                    <div class="dynamic-mini-box">
                                        <span>{{ $isArabic ? 'النوع' : 'Type' }}</span>
                                        <strong>{{ $field->type }}</strong>
                                    </div>

                                    <div class="dynamic-mini-box">
                                        <span>{{ $isArabic ? 'الترتيب' : 'Sort order' }}</span>
                                        <strong>{{ $field->sort_order }}</strong>
                                    </div>

                                    <div class="dynamic-mini-box">
                                        <span>{{ $isArabic ? 'التصنيف' : 'Category' }}</span>
                                        <strong>{{ $field->category?->name_ar ?? '—' }}</strong>
                                    </div>

                                    <div class="dynamic-mini-box">
                                        <span>{{ $isArabic ? 'الفرعي' : 'Subcategory' }}</span>
                                        <strong>{{ $field->subcategory?->name_ar ?? '—' }}</strong>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="dynamic-pagination">
                        {{ $fields->withQueryString()->links() }}
                    </div>
                @else
                    <div class="dynamic-empty">
                        {{ $isArabic ? 'لا توجد حقول ديناميكية حالياً.' : 'There are no dynamic fields yet.' }}
                    </div>
                @endif
            </div>

            <div style="display:grid; gap:20px;">
                <div class="dynamic-detail-panel">
                    <div class="dynamic-panel-head">
                        <h2 class="dynamic-panel-title">{{ $isArabic ? 'تفاصيل الحقل المحدد' : 'Selected field details' }}</h2>
                    </div>

                    @if ($selectedField)
                        <h3 class="dynamic-detail-title">{{ $selectedField->label_ar }}</h3>
                        <div class="dynamic-detail-sub">{{ $selectedField->label_en ?: $selectedField->key }}</div>

                        <div class="dynamic-detail-grid">
                            <div class="dynamic-detail-box">
                                <span>Key</span>
                                <strong>{{ $selectedField->key }}</strong>
                            </div>

                            <div class="dynamic-detail-box">
                                <span>{{ $isArabic ? 'النوع' : 'Type' }}</span>
                                <strong>{{ $selectedField->type }}</strong>
                            </div>

                            <div class="dynamic-detail-box">
                                <span>{{ $isArabic ? 'الترتيب' : 'Sort order' }}</span>
                                <strong>{{ $selectedField->sort_order }}</strong>
                            </div>

                            <div class="dynamic-detail-box">
                                <span>{{ $isArabic ? 'التصنيف' : 'Category' }}</span>
                                <strong>{{ $selectedField->category?->name_ar ?? '—' }}</strong>
                            </div>

                            <div class="dynamic-detail-box">
                                <span>{{ $isArabic ? 'التصنيف الفرعي' : 'Subcategory' }}</span>
                                <strong>{{ $selectedField->subcategory?->name_ar ?? '—' }}</strong>
                            </div>

                            <div class="dynamic-detail-box">
                                <span>{{ $isArabic ? 'الحالة' : 'Status' }}</span>
                                <strong>
                                    {{ $selectedField->is_active
                                        ? ($isArabic ? 'مفعّل' : 'Active')
                                        : ($isArabic ? 'معطل' : 'Inactive') }}
                                </strong>
                            </div>
                        </div>

                        @if ($selectedField->type === 'select' && is_array($selectedField->options) && count($selectedField->options))
                            <div class="dynamic-options-wrap">
                                <h3>{{ $isArabic ? 'خيارات الحقل' : 'Field options' }}</h3>

                                <div class="dynamic-options-list">
                                    @foreach ($selectedField->options as $option)
                                        <div class="dynamic-option-item">{{ $option }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="dynamic-options-wrap">
                            <h3>{{ $isArabic ? 'معاينة JSON للحقل' : 'Field JSON Preview' }}</h3>
                            <pre class="dynamic-json-box" id="fieldJsonPreview">{{ json_encode([
                                'id' => $selectedField->id,
                                'category_id' => $selectedField->category_id,
                                'subcategory_id' => $selectedField->subcategory_id,
                                'label_ar' => $selectedField->label_ar,
                                'label_en' => $selectedField->label_en,
                                'key' => $selectedField->key,
                                'type' => $selectedField->type,
                                'is_required' => $selectedField->is_required,
                                'is_active' => $selectedField->is_active,
                                'sort_order' => $selectedField->sort_order,
                                'options' => $selectedField->options,
                            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    @else
                        <div class="dynamic-empty">
                            {{ $isArabic ? 'اختر حقلاً لعرض التفاصيل والمعاينة.' : 'Select a field to view details and preview.' }}
                        </div>
                    @endif
                </div>

                <div class="dynamic-form-panel">
                    <div class="dynamic-panel-head">
                        <h2 class="dynamic-panel-title">
                            {{ $selectedField
                                ? ($isArabic ? 'تعديل الحقل الديناميكي' : 'Edit dynamic field')
                                : ($isArabic ? 'إضافة حقل ديناميكي' : 'Create dynamic field') }}
                        </h2>
                    </div>

                    <form
                        method="POST"
                        action="{{ $selectedField ? route('admin.dynamic-fields.update', $selectedField->id) : route('admin.dynamic-fields.store') }}"
                        class="dynamic-form"
                    >
                        @csrf
                        @if ($selectedField)
                            @method('PUT')
                        @endif

                        <div class="dynamic-form-grid">
                            <div class="dynamic-group">
                                <label class="dynamic-label">{{ $isArabic ? 'الاسم بالعربية' : 'Arabic label' }}</label>
                                <input type="text" name="label_ar" class="dynamic-input" value="{{ old('label_ar', $selectedField->label_ar ?? '') }}">
                                @error('label_ar') <div class="dynamic-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="dynamic-group">
                                <label class="dynamic-label">{{ $isArabic ? 'الاسم بالإنجليزية' : 'English label' }}</label>
                                <input type="text" name="label_en" class="dynamic-input" value="{{ old('label_en', $selectedField->label_en ?? '') }}">
                                @error('label_en') <div class="dynamic-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="dynamic-group">
                                <label class="dynamic-label">Key</label>
                                <input type="text" name="key" class="dynamic-input" value="{{ old('key', $selectedField->key ?? '') }}">
                                @error('key') <div class="dynamic-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="dynamic-group">
                                <label class="dynamic-label">{{ $isArabic ? 'النوع' : 'Type' }}</label>
                                <select name="type" class="dynamic-select" id="fieldTypeSelect">
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" @selected(old('type', $selectedField->type ?? '') === $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('type') <div class="dynamic-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="dynamic-group">
                                <label class="dynamic-label">{{ $isArabic ? 'التصنيف الرئيسي' : 'Category' }}</label>
                                <select name="category_id" class="dynamic-select">
                                    <option value="">{{ $isArabic ? 'بدون' : 'None' }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id', $selectedField->category_id ?? '') == $category->id)>
                                            {{ $category->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="dynamic-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="dynamic-group">
                                <label class="dynamic-label">{{ $isArabic ? 'التصنيف الفرعي' : 'Subcategory' }}</label>
                                <select name="subcategory_id" class="dynamic-select">
                                    <option value="">{{ $isArabic ? 'بدون' : 'None' }}</option>
                                    @foreach($subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}" @selected(old('subcategory_id', $selectedField->subcategory_id ?? '') == $subcategory->id)>
                                            {{ $subcategory->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subcategory_id') <div class="dynamic-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="dynamic-group">
                                <label class="dynamic-label">{{ $isArabic ? 'الترتيب' : 'Sort order' }}</label>
                                <input type="number" name="sort_order" class="dynamic-input" value="{{ old('sort_order', $selectedField->sort_order ?? 0) }}">
                                @error('sort_order') <div class="dynamic-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="dynamic-group">
                                <label class="dynamic-label">{{ $isArabic ? 'الحالة' : 'Status' }}</label>
                                <select name="is_active" class="dynamic-select">
                                    <option value="1" @selected(old('is_active', $selectedField->is_active ?? true) == 1)>{{ $isArabic ? 'مفعّل' : 'Active' }}</option>
                                    <option value="0" @selected(old('is_active', $selectedField->is_active ?? true) == 0)>{{ $isArabic ? 'معطل' : 'Inactive' }}</option>
                                </select>
                                @error('is_active') <div class="dynamic-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="dynamic-group">
                            <label class="dynamic-label">{{ $isArabic ? 'خيارات select مفصولة بفواصل' : 'Select options separated by commas' }}</label>
                            <textarea name="options_text" class="dynamic-textarea">{{ old('options_text', isset($selectedField) && is_array($selectedField->options) ? implode(', ', $selectedField->options) : '') }}</textarea>
                            @error('options_text') <div class="dynamic-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="dynamic-checks">
                            <label class="dynamic-check-item">
                                <input type="hidden" name="is_required" value="0">
                                <input type="checkbox" name="is_required" value="1" @checked(old('is_required', $selectedField->is_required ?? false))>
                                <span>{{ $isArabic ? 'حقل مطلوب' : 'Required field' }}</span>
                            </label>
                        </div>

                        <div class="dynamic-actions">
                            <button type="submit" class="dynamic-btn-primary">
                                {{ $selectedField
                                    ? ($isArabic ? 'حفظ التعديلات' : 'Save changes')
                                    : ($isArabic ? 'إضافة الحقل' : 'Create field') }}
                            </button>

                            @if ($selectedField)
                                <a href="{{ route('admin.dynamic-fields.index') }}" class="dynamic-btn-secondary">
                                    {{ $isArabic ? 'حقل جديد' : 'New field' }}
                                </a>
                            @endif
                        </div>
                    </form>

                    @if ($selectedField)
                        <form method="POST" action="{{ route('admin.dynamic-fields.destroy', $selectedField->id) }}" style="margin-top:14px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dynamic-btn-danger">
                                {{ $isArabic ? 'حذف الحقل' : 'Delete field' }}
                            </button>
                        </form>
                    @endif
                </div>

                <div class="dynamic-filter-panel">
                    <div class="dynamic-panel-head">
                        <h2 class="dynamic-panel-title">{{ $isArabic ? 'معاينة الفلترة' : 'Filter Preview' }}</h2>
                    </div>

                    @if ($selectedField)
                        <p class="dynamic-filter-help">
                            {{ $isArabic
                                ? 'هذه المعاينة توضح كيف سيتم إرسال الفلتر لهذا الحقل عند استخدامه في صفحة الخدمات أو من تطبيق الموبايل.'
                                : 'This preview shows how filtering for the selected field will be sent from services screens or the mobile app.' }}
                        </p>

                        <div class="dynamic-filter-grid">
                            <div class="dynamic-group">
                                <label class="dynamic-label">{{ $isArabic ? 'نوع الحقل' : 'Field type' }}</label>
                                <input type="text" class="dynamic-input" value="{{ $selectedField->type }}" disabled>
                            </div>

                            <div class="dynamic-group">
                                <label class="dynamic-label">{{ $isArabic ? 'المعامل' : 'Operator' }}</label>
                                <select class="dynamic-select" id="filterOperator"></select>
                            </div>
                        </div>

                        <div class="dynamic-filter-grid" id="singleValueWrap" style="margin-top:14px;">
                            <div class="dynamic-group" style="grid-column:1 / -1;">
                                <label class="dynamic-label">{{ $isArabic ? 'القيمة' : 'Value' }}</label>
                                <input type="text" class="dynamic-input" id="filterValue" placeholder="{{ $isArabic ? 'أدخل قيمة الفلتر' : 'Enter filter value' }}">
                            </div>
                        </div>

                        <div class="dynamic-filter-grid" id="betweenWrap" style="display:none; margin-top:14px;">
                            <div class="dynamic-group">
                                <label class="dynamic-label">{{ $isArabic ? 'من' : 'From' }}</label>
                                <input type="text" class="dynamic-input" id="filterFrom" placeholder="{{ $isArabic ? 'القيمة الأولى' : 'First value' }}">
                            </div>

                            <div class="dynamic-group">
                                <label class="dynamic-label">{{ $isArabic ? 'إلى' : 'To' }}</label>
                                <input type="text" class="dynamic-input" id="filterTo" placeholder="{{ $isArabic ? 'القيمة الثانية' : 'Second value' }}">
                            </div>
                        </div>

                        <div class="dynamic-options-wrap">
                            <h3>{{ $isArabic ? 'شكل الفلتر JSON' : 'Filter JSON Payload' }}</h3>
                            <pre class="dynamic-json-box" id="filterJsonPreview"></pre>
                        </div>

                        <div class="dynamic-options-wrap">
                            <h3>{{ $isArabic ? 'شكل الرابط Query String' : 'Query String Preview' }}</h3>
                            <pre class="dynamic-json-box" id="filterQueryPreview"></pre>
                        </div>
                    @else
                        <div class="dynamic-empty">
                            {{ $isArabic ? 'اختر حقلاً أولاً حتى تظهر معاينة الفلترة.' : 'Select a field first to view filter preview.' }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    @if ($selectedField)
        <script>
            (() => {
                const fieldId = @json($selectedField->id);
                const fieldType = @json($selectedField->type);
                const operatorEl = document.getElementById('filterOperator');
                const valueEl = document.getElementById('filterValue');
                const fromEl = document.getElementById('filterFrom');
                const toEl = document.getElementById('filterTo');
                const singleWrap = document.getElementById('singleValueWrap');
                const betweenWrap = document.getElementById('betweenWrap');
                const jsonPreview = document.getElementById('filterJsonPreview');
                const queryPreview = document.getElementById('filterQueryPreview');

                const operatorMap = {
                    text: ['eq'],
                    textarea: ['eq'],
                    select: ['eq'],
                    boolean: ['eq'],
                    number: ['eq', 'gt', 'gte', 'lt', 'lte', 'between'],
                    date: ['eq', 'before', 'after', 'between_date']
                };

                const labels = {
                    eq: 'eq',
                    gt: 'gt',
                    gte: 'gte',
                    lt: 'lt',
                    lte: 'lte',
                    between: 'between',
                    before: 'before',
                    after: 'after',
                    between_date: 'between_date'
                };

                function setOperators() {
                    const operators = operatorMap[fieldType] || ['eq'];
                    operatorEl.innerHTML = operators.map(op => `<option value="${op}">${labels[op]}</option>`).join('');
                }

                function toggleInputs() {
                    const operator = operatorEl.value;
                    const isBetween = operator === 'between' || operator === 'between_date';

                    singleWrap.style.display = isBetween ? 'none' : 'grid';
                    betweenWrap.style.display = isBetween ? 'grid' : 'none';

                    if (fieldType === 'boolean' && !isBetween) {
                        valueEl.placeholder = 'true / false / 1 / 0';
                    } else if (fieldType === 'date') {
                        valueEl.placeholder = 'YYYY-MM-DD';
                        fromEl.placeholder = 'YYYY-MM-DD';
                        toEl.placeholder = 'YYYY-MM-DD';
                    } else if (fieldType === 'number') {
                        valueEl.placeholder = '100';
                        fromEl.placeholder = '100';
                        toEl.placeholder = '200';
                    } else {
                        valueEl.placeholder = 'value';
                    }
                }

                function buildPayload() {
                    const operator = operatorEl.value;
                    let payload = {
                        dynamic_filters: {}
                    };

                    if (operator === 'between' || operator === 'between_date') {
                        payload.dynamic_filters[fieldId] = {
                            operator: operator,
                            from: fromEl.value || '',
                            to: toEl.value || ''
                        };
                    } else {
                        payload.dynamic_filters[fieldId] = {
                            operator: operator,
                            value: valueEl.value || ''
                        };
                    }

                    jsonPreview.textContent = JSON.stringify(payload, null, 2);

                    let query = '';

                    if (operator === 'between' || operator === 'between_date') {
                        query =
                            `/api/v1/services?dynamic_filters[${fieldId}][operator]=${encodeURIComponent(operator)}` +
                            `&dynamic_filters[${fieldId}][from]=${encodeURIComponent(fromEl.value || '')}` +
                            `&dynamic_filters[${fieldId}][to]=${encodeURIComponent(toEl.value || '')}`;
                    } else {
                        query =
                            `/api/v1/services?dynamic_filters[${fieldId}][operator]=${encodeURIComponent(operator)}` +
                            `&dynamic_filters[${fieldId}][value]=${encodeURIComponent(valueEl.value || '')}`;
                    }

                    queryPreview.textContent = query;
                }

                setOperators();
                toggleInputs();
                buildPayload();

                operatorEl.addEventListener('change', () => {
                    toggleInputs();
                    buildPayload();
                });

                [valueEl, fromEl, toEl].forEach(el => {
                    el.addEventListener('input', buildPayload);
                });
            })();
        </script>
    @endif
@endsection