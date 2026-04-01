@extends('layouts.app')

@section('content')
<section class="estimation-page">
    <div class="container">
        <div class="estimation-shell">
            <div class="estimation-hero">
                <div class="hero-content">
                    <span class="hero-badge">
                        {{ __('ui.smart_estimation') ?? 'Smart Estimation' }}
                    </span>

                    <h1 class="hero-title">
                        {{ __('messages.estimation_title') ?? 'حاسبة التقدير الذكي' }}
                    </h1>

                    <p class="hero-subtitle">
                        {{ __('messages.estimation_subtitle') ?? 'احسب الكميات والتكلفة التقديرية لمشروعك بسهولة ودقة' }}
                    </p>

                    <div class="hero-points">
                        <div class="hero-point">
                            <span class="hero-point-icon">📐</span>
                            <span>{{ __('messages.measurements') ?? 'حساب المساحات' }}</span>
                        </div>
                        <div class="hero-point">
                            <span class="hero-point-icon">🧱</span>
                            <span>{{ __('messages.materials_quantities') ?? 'تقدير المواد' }}</span>
                        </div>
                        <div class="hero-point">
                            <span class="hero-point-icon">💰</span>
                            <span>{{ __('messages.estimated_cost') ?? 'التكلفة التقديرية' }}</span>
                        </div>
                        <div class="hero-point">
                            <span class="hero-point-icon">🔗</span>
                            <span>{{ __('messages.service_matching') ?? 'ربط الخدمات' }}</span>
                        </div>
                    </div>
                </div>

                <div class="hero-side">
                    <div class="hero-side-card">
                        <div class="hero-side-header">
                            <h3>{{ __('messages.why_use_estimation') ?? 'لماذا تستخدم هذه الأداة؟' }}</h3>
                            <span class="status-pill">{{ app()->getLocale() === 'ar' ? 'احترافي' : 'Professional' }}</span>
                        </div>

                        <p class="hero-side-text">
                            {{ __('messages.estimation_description') ?? 'تساعدك هذه الصفحة على حساب المساحات والكميات والتكلفة التقديرية وربطها بالخدمات المناسبة داخل المنصة.' }}
                        </p>

                        <div class="hero-side-list">
                            <div class="hero-side-item">
                                <strong>{{ app()->getLocale() === 'ar' ? 'دقة مبدئية' : 'Initial Accuracy' }}</strong>
                                <span>{{ app()->getLocale() === 'ar' ? 'يعطيك تصور أولي واضح قبل التنفيذ.' : 'Gives you a clear initial view before execution.' }}</span>
                            </div>

                            <div class="hero-side-item">
                                <strong>{{ app()->getLocale() === 'ar' ? 'سرعة القرار' : 'Faster Decisions' }}</strong>
                                <span>{{ app()->getLocale() === 'ar' ? 'يساعد المستخدم على المقارنة بسرعة.' : 'Helps users compare options quickly.' }}</span>
                            </div>

                            <div class="hero-side-item">
                                <strong>{{ app()->getLocale() === 'ar' ? 'ربط مباشر' : 'Direct Matching' }}</strong>
                                <span>{{ app()->getLocale() === 'ar' ? 'يوصل النتائج بالخدمات المناسبة داخل المنصة.' : 'Connects results to relevant services inside the platform.' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="estimation-grid">
                <div class="estimation-form-card">
                    <div class="section-head">
                        <div>
                            <h2>{{ __('messages.estimation_form') ?? 'بيانات المشروع' }}</h2>
                            <p>{{ __('messages.fill_estimation_form') ?? 'أدخلي المعلومات المطلوبة للحصول على تقدير فوري.' }}</p>
                        </div>
                        <div class="section-tag">
                            {{ app()->getLocale() === 'ar' ? 'إدخال سريع' : 'Quick Input' }}
                        </div>
                    </div>

                    <div id="formErrorBox" class="form-alert d-none"></div>

                    <div id="typeGuideBox" class="guide-box">
                        <div class="guide-title">
                            {{ app()->getLocale() === 'ar' ? 'تعليمات الإدخال' : 'Input guidance' }}
                        </div>
                        <div class="guide-text" id="typeGuideText">
                            {{ app()->getLocale() === 'ar'
                                ? 'اختاري نوع العمل أولاً لتظهر لك التعليمات المناسبة.'
                                : 'Select the work type first to see the appropriate instructions.' }}
                        </div>
                    </div>

                    <form id="estimationForm">
                        @csrf

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    {{ __('messages.city') ?? 'المدينة' }}
                                </label>
                                <select class="form-select custom-input" name="city_id" id="citySelect" required>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">
                                            {{ app()->getLocale() === 'ar' ? ($city->name_ar ?? $city->name_en) : ($city->name_en ?? $city->name_ar) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    {{ __('messages.work_type') ?? 'نوع العمل' }}
                                </label>
                                <select class="form-select custom-input" name="estimation_type_id" id="workTypeSelect" required>
                                    @foreach($types as $type)
                                        <option
                                            value="{{ $type->id }}"
                                            data-code="{{ $type->code }}"
                                            data-name-ar="{{ $type->name_ar ?? $type->name_en }}"
                                            data-name-en="{{ $type->name_en ?? $type->name_ar }}"
                                        >
                                            {{ app()->getLocale() === 'ar' ? ($type->name_ar ?? $type->name_en) : ($type->name_en ?? $type->name_ar) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group form-group-full">
                                <label class="form-label">
                                    {{ app()->getLocale() === 'ar' ? 'وحدة الإدخال' : 'Input unit' }}
                                </label>
                                <select class="form-select custom-input" id="inputUnit">
                                    <option value="m">{{ app()->getLocale() === 'ar' ? 'متر' : 'Meter' }}</option>
                                    <option value="cm">{{ app()->getLocale() === 'ar' ? 'سنتيمتر' : 'Centimeter' }}</option>
                                </select>
                                <div class="input-help">
                                    {{ app()->getLocale() === 'ar'
                                        ? 'إذا أدخلت القيم بالسنتيمتر فسيتم تحويلها تلقائيًا إلى متر قبل الحساب.'
                                        : 'If you enter values in centimeters, they will be converted automatically to meters before calculation.' }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    {{ __('messages.length') ?? 'الطول' }}
                                    <span class="unit-chip" id="lengthUnitLabel">{{ app()->getLocale() === 'ar' ? 'متر' : 'm' }}</span>
                                </label>
                                <input type="number" step="0.01" min="0" name="length" id="lengthInput" class="form-control custom-input" placeholder="0.00">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    {{ __('messages.width') ?? 'العرض' }}
                                    <span class="unit-chip" id="widthUnitLabel">{{ app()->getLocale() === 'ar' ? 'متر' : 'm' }}</span>
                                </label>
                                <input type="number" step="0.01" min="0" name="width" id="widthInput" class="form-control custom-input" placeholder="0.00">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    {{ __('messages.height') ?? 'الارتفاع' }}
                                    <span class="unit-chip" id="heightUnitLabel">{{ app()->getLocale() === 'ar' ? 'متر' : 'm' }}</span>
                                </label>
                                <input type="number" step="0.01" min="0" name="height" id="heightInput" class="form-control custom-input" placeholder="0.00">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    {{ __('messages.ready_area') ?? 'المساحة الجاهزة (اختياري)' }}
                                    <span class="unit-chip" id="areaUnitLabel">{{ app()->getLocale() === 'ar' ? 'م²' : 'm²' }}</span>
                                </label>
                                <input type="number" step="0.01" min="0" name="area" id="areaInput" class="form-control custom-input" placeholder="0.00">
                            </div>
                        </div>

                        <div class="smart-hint-row">
                            <div class="smart-hint-card">
                                <strong>{{ app()->getLocale() === 'ar' ? 'مهم' : 'Important' }}</strong>
                                <span id="smartHintText">
                                    {{ app()->getLocale() === 'ar'
                                        ? 'القيم المدخلة تُحسب داخليًا بالمتر والمتر المربع.'
                                        : 'Entered values are calculated internally in meters and square meters.' }}
                                </span>
                            </div>
                        </div>

                        <div class="form-footer">
                            <div class="form-note">
                                {{ app()->getLocale() === 'ar'
                                    ? 'هذا التقدير أولي ويعتمد على البيانات والأسعار المتوفرة. كل الحسابات تقريبية لدعم اتخاذ القرار.'
                                    : 'This estimate is preliminary and depends on available data and prices. All calculations are approximate for decision support.' }}
                            </div>

                            <button type="submit" class="btn estimation-btn">
                                <span id="submitText">{{ __('messages.calculate_estimation') ?? 'احسب التقدير' }}</span>
                                <span id="submitSpinner" class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="estimation-preview-card">
                    <div class="section-head preview-head">
                        <div>
                            <h2>{{ app()->getLocale() === 'ar' ? 'ملخص سريع' : 'Quick Summary' }}</h2>
                            <p>
                                {{ app()->getLocale() === 'ar'
                                    ? 'نظرة أولية على ما ستحصل عليه بعد تنفيذ الحساب.'
                                    : 'A quick overview of what you will get after calculation.' }}
                            </p>
                        </div>
                    </div>

                    <div class="preview-stack">
                        <div class="preview-item">
                            <div class="preview-icon">01</div>
                            <div>
                                <h4>{{ app()->getLocale() === 'ar' ? 'إدخال البيانات' : 'Input Data' }}</h4>
                                <p>{{ app()->getLocale() === 'ar' ? 'اختيار المدينة ونوع العمل وإدخال الأبعاد بشكل واضح.' : 'Choose city, work type, and enter dimensions clearly.' }}</p>
                            </div>
                        </div>

                        <div class="preview-item">
                            <div class="preview-icon">02</div>
                            <div>
                                <h4>{{ app()->getLocale() === 'ar' ? 'الحساب التقديري' : 'Estimation Calculation' }}</h4>
                                <p>{{ app()->getLocale() === 'ar' ? 'حساب المساحة والكميات والتكلفة والمدة.' : 'Calculate area, quantities, cost, and duration.' }}</p>
                            </div>
                        </div>

                        <div class="preview-item">
                            <div class="preview-icon">03</div>
                            <div>
                                <h4>{{ app()->getLocale() === 'ar' ? 'عرض النتائج' : 'Results Output' }}</h4>
                                <p>{{ app()->getLocale() === 'ar' ? 'إظهار المواد المطلوبة والخدمات المقترحة بشكل أوضح.' : 'Show required materials and suggested services more clearly.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="resultBox" class="result-wrapper d-none">
                <div class="result-card">
                    <div class="section-head">
                        <div>
                            <h2>{{ __('messages.results') ?? 'النتائج' }}</h2>
                            <p>{{ __('messages.estimation_ready') ?? 'تم حساب النتائج التقديرية بنجاح.' }}</p>
                        </div>
                        <div class="section-tag success-tag">
                            {{ app()->getLocale() === 'ar' ? 'تم الحساب' : 'Calculated' }}
                        </div>
                    </div>

                    <div class="input-summary-card">
                        <div class="input-summary-title">
                            {{ app()->getLocale() === 'ar' ? 'ملخص الإدخال' : 'Input summary' }}
                        </div>
                        <div class="input-summary-grid" id="inputSummaryGrid"></div>
                    </div>

                    <div class="summary-grid">
                        <div class="summary-box">
                            <div class="summary-label">{{ __('messages.total_cost') ?? 'التكلفة الإجمالية' }}</div>
                            <div class="summary-value text-primary" id="totalCost">0</div>
                        </div>

                        <div class="summary-box">
                            <div class="summary-label">{{ __('messages.duration') ?? 'مدة التنفيذ' }}</div>
                            <div class="summary-value" id="durationDays">-</div>
                        </div>
                    </div>

                    <div class="result-section">
                        <div class="result-section-head">
                            <h3>{{ __('messages.required_materials') ?? 'المواد المطلوبة' }}</h3>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle estimation-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.material') ?? 'المادة' }}</th>
                                        <th>{{ __('messages.base_qty') ?? 'الكمية الأساسية' }}</th>
                                        <th>{{ __('messages.waste') ?? 'الهدر' }}</th>
                                        <th>{{ __('messages.final_qty') ?? 'الكمية النهائية' }}</th>
                                        <th>{{ __('messages.price') ?? 'السعر' }}</th>
                                        <th>{{ __('messages.total') ?? 'الإجمالي' }}</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="result-section">
                        <div class="result-section-head">
                            <h3>{{ __('messages.suggested_services') ?? 'الخدمات المقترحة' }}</h3>
                        </div>
                        <div id="serviceMatches" class="service-match-list"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .estimation-page {
        padding: 20px 0 40px;
    }

    .estimation-shell {
        display: grid;
        gap: 24px;
    }

    .estimation-hero {
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 22px;
        align-items: stretch;
    }

    .hero-content,
    .hero-side-card,
    .estimation-form-card,
    .estimation-preview-card,
    .result-card {
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid rgba(15, 23, 42, 0.06);
        box-shadow: 0 18px 50px rgba(15, 23, 42, 0.06);
        border-radius: 28px;
    }

    .hero-content {
        padding: 34px;
        background: linear-gradient(135deg, #182848 0%, #243b73 100%);
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .hero-content::before {
        content: "";
        position: absolute;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        top: -80px;
        inset-inline-end: -60px;
        background: radial-gradient(circle, rgba(255,255,255,0.15), transparent 65%);
    }

    .hero-content::after {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        bottom: -100px;
        inset-inline-start: -50px;
        background: radial-gradient(circle, rgba(255,255,255,0.09), transparent 65%);
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.16);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
    }

    .hero-title {
        position: relative;
        z-index: 1;
        margin: 0 0 12px;
        font-size: clamp(2rem, 4vw, 3rem);
        line-height: 1.08;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .hero-subtitle {
        position: relative;
        z-index: 1;
        margin: 0;
        color: rgba(255,255,255,0.85);
        font-size: 15px;
        line-height: 1.95;
        max-width: 760px;
    }

    .hero-points {
        position: relative;
        z-index: 1;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 22px;
    }

    .hero-point {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 14px;
        border-radius: 999px;
        background: rgba(255,255,255,0.10);
        border: 1px solid rgba(255,255,255,0.14);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
    }

    .hero-point-icon {
        font-size: 15px;
    }

    .hero-side-card {
        padding: 28px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        height: 100%;
    }

    .hero-side-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .hero-side-header h3 {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        color: #1f2f4d;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        background: rgba(79,70,229,0.08);
        color: #4338ca;
        font-size: 12px;
        font-weight: 700;
    }

    .hero-side-text {
        margin: 0 0 18px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.95;
    }

    .hero-side-list {
        display: grid;
        gap: 14px;
    }

    .hero-side-item {
        padding: 14px 16px;
        border: 1px solid #edf2f7;
        border-radius: 18px;
        background: #fff;
    }

    .hero-side-item strong {
        display: block;
        margin-bottom: 4px;
        font-size: 14px;
        color: #1f2f4d;
    }

    .hero-side-item span {
        display: block;
        font-size: 13px;
        color: #64748b;
        line-height: 1.8;
    }

    .estimation-grid {
        display: grid;
        grid-template-columns: 1.08fr 0.92fr;
        gap: 22px;
        align-items: start;
    }

    .estimation-form-card,
    .estimation-preview-card,
    .result-card {
        padding: 28px;
    }

    .section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }

    .section-head h2 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 800;
        color: #1f2f4d;
    }

    .section-head p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.8;
    }

    .section-tag {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 0 12px;
        border-radius: 999px;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-size: 12px;
        font-weight: 700;
    }

    .success-tag {
        background: rgba(5,150,105,0.08);
        color: #059669;
        border-color: rgba(5,150,105,0.14);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .form-group-full {
        grid-column: 1 / -1;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 8px;
        color: #1f2937;
        font-size: 13px;
        font-weight: 700;
    }

    .unit-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        padding: 0 10px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        font-size: 11px;
        font-weight: 800;
    }

    .input-help {
        margin-top: 8px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.8;
    }

    .custom-input {
        border-radius: 18px;
        min-height: 54px;
        border: 1px solid #dbe4f0;
        background: #fff;
        box-shadow: none;
        font-size: 14px;
    }

    .custom-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.12);
    }

    .guide-box {
        margin-bottom: 18px;
        padding: 16px 18px;
        border-radius: 18px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        border: 1px solid #e6edf7;
    }

    .guide-title {
        font-size: 14px;
        font-weight: 800;
        color: #1f2f4d;
        margin-bottom: 6px;
    }

    .guide-text {
        color: #64748b;
        font-size: 13px;
        line-height: 1.9;
    }

    .smart-hint-row {
        margin-top: 18px;
    }

    .smart-hint-card {
        padding: 14px 16px;
        border-radius: 18px;
        background: #fafbff;
        border: 1px solid rgba(15,23,42,.06);
        display: grid;
        gap: 4px;
    }

    .smart-hint-card strong {
        color: #1f2f4d;
        font-size: 13px;
        font-weight: 800;
    }

    .smart-hint-card span {
        color: #64748b;
        font-size: 13px;
        line-height: 1.8;
    }

    .form-alert {
        padding: 14px 16px;
        border-radius: 18px;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 18px;
    }

    .form-alert.error {
        background: rgba(239,68,68,0.10);
        color: #dc2626;
        border: 1px solid rgba(239,68,68,0.12);
    }

    .form-footer {
        margin-top: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .form-note {
        color: #64748b;
        font-size: 13px;
        line-height: 1.8;
        max-width: 520px;
    }

    .estimation-btn {
        min-width: 210px;
        min-height: 54px;
        border: 0;
        border-radius: 18px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: #fff;
        font-weight: 700;
        transition: 0.25s ease;
        box-shadow: 0 16px 28px rgba(79, 70, 229, 0.18);
    }

    .estimation-btn:hover {
        transform: translateY(-1px);
        color: #fff;
        box-shadow: 0 18px 30px rgba(79, 70, 229, 0.24);
    }

    .preview-stack {
        display: grid;
        gap: 14px;
    }

    .preview-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px;
        border-radius: 20px;
        border: 1px solid #edf2f7;
        background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
    }

    .preview-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: rgba(79,70,229,0.08);
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .preview-item h4 {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 800;
        color: #1f2f4d;
    }

    .preview-item p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
        line-height: 1.8;
    }

    .result-wrapper {
        display: block;
    }

    .input-summary-card {
        margin-bottom: 22px;
        padding: 18px;
        border-radius: 20px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        border: 1px solid #e6edf7;
    }

    .input-summary-title {
        font-size: 15px;
        font-weight: 800;
        color: #1f2f4d;
        margin-bottom: 12px;
    }

    .input-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .input-summary-item {
        padding: 14px;
        border-radius: 16px;
        background: #fff;
        border: 1px solid rgba(15,23,42,.06);
    }

    .input-summary-item span {
        display: block;
        margin-bottom: 5px;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
    }

    .input-summary-item strong {
        display: block;
        color: #1f2f4d;
        font-size: 14px;
        font-weight: 800;
        line-height: 1.7;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .summary-box {
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        border: 1px solid #e6edf7;
        border-radius: 20px;
        padding: 22px;
        min-height: 122px;
    }

    .summary-label {
        color: #64748b;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 700;
    }

    .summary-value {
        font-size: 1.95rem;
        font-weight: 800;
        color: #1f2f4d;
        line-height: 1.15;
    }

    .result-section + .result-section {
        margin-top: 26px;
    }

    .result-section-head {
        margin-bottom: 14px;
    }

    .result-section-head h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #1f2f4d;
    }

    .estimation-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .estimation-table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e5edf7;
        color: #334155;
        font-size: 13px;
        font-weight: 800;
        white-space: nowrap;
        padding: 14px;
    }

    .estimation-table tbody td {
        padding: 14px;
        font-size: 13px;
        color: #475569;
        border-color: #eef2f7;
        vertical-align: middle;
    }

    .estimation-table tbody tr:hover {
        background: #fcfdff;
    }

    .service-match-list {
        display: grid;
        gap: 12px;
    }

    .service-card-mini {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        border: 1px solid #e6edf7;
        border-radius: 18px;
        padding: 16px 18px;
    }

    .service-card-mini .fw-bold {
        color: #1f2f4d;
        font-size: 14px;
    }

    .service-card-mini .text-muted {
        color: #64748b !important;
        line-height: 1.7;
    }

    .service-price-badge {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: #fff;
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
        box-shadow: 0 12px 24px rgba(79, 70, 229, 0.18);
    }

    .empty-result-box {
        padding: 18px;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px dashed #dbe4f0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.8;
        text-align: center;
    }

    @media (max-width: 1199px) {
        .estimation-hero,
        .estimation-grid,
        .input-summary-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .estimation-page {
            padding: 8px 0 26px;
        }

        .hero-content,
        .hero-side-card,
        .estimation-form-card,
        .estimation-preview-card,
        .result-card {
            padding: 20px;
            border-radius: 22px;
        }

        .hero-title {
            font-size: 2rem;
        }

        .form-grid,
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .form-footer {
            align-items: stretch;
        }

        .estimation-btn {
            width: 100%;
        }

        .service-card-mini {
            flex-direction: column;
            align-items: flex-start;
        }

        .service-price-badge {
            align-self: flex-start;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const estimationForm = document.getElementById('estimationForm');
    const inputUnit = document.getElementById('inputUnit');
    const workTypeSelect = document.getElementById('workTypeSelect');
    const citySelect = document.getElementById('citySelect');

    const formErrorBox = document.getElementById('formErrorBox');
    const typeGuideText = document.getElementById('typeGuideText');
    const smartHintText = document.getElementById('smartHintText');

    const lengthInput = document.getElementById('lengthInput');
    const widthInput = document.getElementById('widthInput');
    const heightInput = document.getElementById('heightInput');
    const areaInput = document.getElementById('areaInput');

    const lengthUnitLabel = document.getElementById('lengthUnitLabel');
    const widthUnitLabel = document.getElementById('widthUnitLabel');
    const heightUnitLabel = document.getElementById('heightUnitLabel');
    const areaUnitLabel = document.getElementById('areaUnitLabel');

    const resultBox = document.getElementById('resultBox');
    const itemsBody = document.getElementById('itemsTableBody');
    const matchesBox = document.getElementById('serviceMatches');
    const inputSummaryGrid = document.getElementById('inputSummaryGrid');

    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');

    const isArabic = '{{ app()->getLocale() }}' === 'ar';

    const texts = {
        unknown: isArabic ? 'غير محدد' : 'Not specified',
        day: isArabic ? 'يوم' : 'day',
        days: isArabic ? 'أيام' : 'days',
        serviceMatch: isArabic ? 'خدمة مطابقة' : 'Matched service',
        noMatches: isArabic ? 'لا توجد خدمات مقترحة مطابقة حاليًا.' : 'No suggested matching services were found right now.',
        noItems: isArabic ? 'لا توجد مواد محسوبة لهذا التقدير.' : 'No materials were calculated for this estimation.',
        failed: isArabic ? 'فشل الاتصال مع الخادم' : 'Failed to connect to the server',
        unexpected: isArabic ? 'حدث خطأ غير متوقع' : 'Unexpected error',
        length: isArabic ? 'الطول' : 'Length',
        width: isArabic ? 'العرض' : 'Width',
        height: isArabic ? 'الارتفاع' : 'Height',
        area: isArabic ? 'المساحة' : 'Area',
        city: isArabic ? 'المدينة' : 'City',
        workType: isArabic ? 'نوع العمل' : 'Work type',
        unit: isArabic ? 'وحدة الإدخال' : 'Input unit',
        totalCost: isArabic ? 'التكلفة الإجمالية' : 'Total cost',
        inputSummary: isArabic ? 'ملخص الإدخال' : 'Input summary',
        chooseValues: isArabic ? 'يرجى تعبئة القيم المطلوبة لنوع العمل المختار.' : 'Please fill the required values for the selected work type.',
        positiveValues: isArabic ? 'يجب أن تكون القيم أكبر من صفر.' : 'Values must be greater than zero.',
        invalidArea: isArabic ? 'المساحة يجب أن تكون أكبر من صفر إذا تم إدخالها.' : 'Area must be greater than zero if provided.',
        calculating: isArabic ? 'جاري الحساب...' : 'Calculating...',
        calcButton: isArabic ? 'احسب التقدير' : 'Calculate estimation',
        score: 'Score'
    };

    function showError(message) {
        formErrorBox.classList.remove('d-none');
        formErrorBox.classList.add('error');
        formErrorBox.textContent = message;
    }

    function clearError() {
        formErrorBox.classList.add('d-none');
        formErrorBox.textContent = '';
    }

    function formatNumber(value, decimals = 2) {
        const number = Number(value ?? 0);
        if (Number.isNaN(number)) return '-';

        return new Intl.NumberFormat(isArabic ? 'ar-SY' : 'en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    }

    function formatDays(days) {
        if (!days || Number(days) <= 0) {
            return texts.unknown;
        }

        const value = Number(days);

        if (isArabic) {
            return value + ' ' + (value === 1 ? texts.day : texts.days);
        }

        return value + ' ' + (value === 1 ? texts.day : texts.days);
    }

    function formatMatchType(matchType) {
        if (!matchType) return texts.serviceMatch;
        if (matchType === 'service') return texts.serviceMatch;
        return matchType;
    }

    function getSelectedTypeCode() {
        return workTypeSelect.options[workTypeSelect.selectedIndex]?.dataset.code || '';
    }

    function getSelectedTypeName() {
        const option = workTypeSelect.options[workTypeSelect.selectedIndex];
        if (!option) return texts.unknown;
        return isArabic ? (option.dataset.nameAr || option.textContent) : (option.dataset.nameEn || option.textContent);
    }

    function getSelectedCityName() {
        const option = citySelect.options[citySelect.selectedIndex];
        return option ? option.textContent.trim() : texts.unknown;
    }

    function refreshUnitLabels() {
        const unit = inputUnit.value;

        if (unit === 'cm') {
            lengthUnitLabel.textContent = isArabic ? 'سم' : 'cm';
            widthUnitLabel.textContent = isArabic ? 'سم' : 'cm';
            heightUnitLabel.textContent = isArabic ? 'سم' : 'cm';
            areaUnitLabel.textContent = isArabic ? 'سم²' : 'cm²';
        } else {
            lengthUnitLabel.textContent = isArabic ? 'متر' : 'm';
            widthUnitLabel.textContent = isArabic ? 'متر' : 'm';
            heightUnitLabel.textContent = isArabic ? 'متر' : 'm';
            areaUnitLabel.textContent = isArabic ? 'م²' : 'm²';
        }
    }

    function refreshTypeGuide() {
        const code = getSelectedTypeCode();

        if (code === 'wall_building') {
            typeGuideText.textContent = isArabic
                ? 'لبناء الجدار يُفضّل إدخال الطول والارتفاع. يمكن ترك العرض فارغًا إذا لم يكن مطلوبًا بالحساب الحالي.'
                : 'For wall building, it is الأفضل to enter length and height. Width can stay empty if not needed in the current formula.';
            smartHintText.textContent = isArabic
                ? 'لبناء الجدار: أدخلي الطول والارتفاع، والنظام سيحسب المساحة الجدارية المطلوبة.'
                : 'For wall building: enter length and height, and the system will calculate the wall area.';
            return;
        }

        if (code === 'painting') {
            typeGuideText.textContent = isArabic
                ? 'للدهان يمكنك إدخال المساحة مباشرة، أو إدخال الطول والعرض، أو الطول والارتفاع حسب طبيعة السطح.'
                : 'For painting, you can enter the area directly, or use length and width, or length and height depending on the surface.';
            smartHintText.textContent = isArabic
                ? 'للدهان: الأفضل إدخال المساحة مباشرة إذا كانت معروفة.'
                : 'For painting: it is best to enter the area directly if it is known.';
            return;
        }

        if (code === 'plastering') {
            typeGuideText.textContent = isArabic
                ? 'للتلبيس يمكنك إدخال المساحة مباشرة، أو استخدام الطول والعرض أو الطول والارتفاع.'
                : 'For plastering, you can enter the area directly, or use length and width or length and height.';
            smartHintText.textContent = isArabic
                ? 'للتلبيس: إذا كانت مساحة السطح معروفة فأدخليها مباشرة لنتيجة أوضح.'
                : 'For plastering: if the surface area is known, enter it directly for a clearer result.';
            return;
        }

        if (code === 'ceramic_installation') {
            typeGuideText.textContent = isArabic
                ? 'للسيراميك يُفضّل إدخال المساحة مباشرة، أو إدخال الطول والعرض لمساحة الأرضية أو الجدار.'
                : 'For ceramic installation, it is best to enter the area directly, or use length and width for the floor or wall area.';
            smartHintText.textContent = isArabic
                ? 'للسيراميك: المساحة أو الطول مع العرض هما الأهم.'
                : 'For ceramics: area or length with width are the most important.';
            return;
        }

        typeGuideText.textContent = isArabic
            ? 'اختاري نوع العمل أولاً لتظهر لك التعليمات المناسبة.'
            : 'Select the work type first to see the appropriate instructions.';
        smartHintText.textContent = isArabic
            ? 'القيم المدخلة تُحسب داخليًا بالمتر والمتر المربع.'
            : 'Entered values are calculated internally in meters and square meters.';
    }

    function validateInputs(payload) {
        const code = getSelectedTypeCode();

        const length = payload.length !== '' ? Number(payload.length) : null;
        const width = payload.width !== '' ? Number(payload.width) : null;
        const height = payload.height !== '' ? Number(payload.height) : null;
        const area = payload.area !== '' ? Number(payload.area) : null;

        const isPositive = (value) => value !== null && !Number.isNaN(value) && value > 0;

        if (payload.length !== '' && !isPositive(length)) return texts.positiveValues;
        if (payload.width !== '' && !isPositive(width)) return texts.positiveValues;
        if (payload.height !== '' && !isPositive(height)) return texts.positiveValues;
        if (payload.area !== '' && !isPositive(area)) return texts.invalidArea;

        if (code === 'wall_building') {
            if (!isPositive(length) || !isPositive(height)) {
                return isArabic
                    ? 'في بناء الجدار يجب إدخال الطول والارتفاع على الأقل.'
                    : 'For wall building, you must enter at least length and height.';
            }
        }

        if (code === 'painting' || code === 'plastering') {
            const valid = isPositive(area) || (isPositive(length) && isPositive(width)) || (isPositive(length) && isPositive(height));
            if (!valid) {
                return isArabic
                    ? 'لهذا النوع يجب إدخال المساحة، أو الطول مع العرض، أو الطول مع الارتفاع.'
                    : 'For this type, enter area, or length and width, or length and height.';
            }
        }

        if (code === 'ceramic_installation') {
            const valid = isPositive(area) || (isPositive(length) && isPositive(width));
            if (!valid) {
                return isArabic
                    ? 'لتركيب السيراميك يجب إدخال المساحة، أو الطول مع العرض.'
                    : 'For ceramic installation, enter area, or length and width.';
            }
        }

        return null;
    }

    function toMeters(value, unit) {
        if (value === '' || value === null || value === undefined) return value;
        const number = Number(value);
        if (Number.isNaN(number)) return value;
        return unit === 'cm' ? (number / 100) : number;
    }

    function toSquareMeters(value, unit) {
        if (value === '' || value === null || value === undefined) return value;
        const number = Number(value);
        if (Number.isNaN(number)) return value;
        return unit === 'cm' ? (number / 10000) : number;
    }

    function renderInputSummary(payload) {
        const unit = inputUnit.value;
        const linearUnitLabel = unit === 'cm' ? (isArabic ? 'سم' : 'cm') : (isArabic ? 'متر' : 'm');
        const areaUnitText = unit === 'cm' ? (isArabic ? 'سم²' : 'cm²') : (isArabic ? 'م²' : 'm²');

        const summaryItems = [
            { label: texts.city, value: getSelectedCityName() },
            { label: texts.workType, value: getSelectedTypeName() },
            { label: texts.unit, value: unit === 'cm' ? (isArabic ? 'سنتيمتر' : 'Centimeter') : (isArabic ? 'متر' : 'Meter') },
            { label: texts.length, value: payload.length ? `${payload.length} ${linearUnitLabel}` : texts.unknown },
            { label: texts.width, value: payload.width ? `${payload.width} ${linearUnitLabel}` : texts.unknown },
            { label: texts.height, value: payload.height ? `${payload.height} ${linearUnitLabel}` : texts.unknown },
            { label: texts.area, value: payload.area ? `${payload.area} ${areaUnitText}` : texts.unknown },
        ];

        inputSummaryGrid.innerHTML = summaryItems.map(item => `
            <div class="input-summary-item">
                <span>${item.label}</span>
                <strong>${item.value}</strong>
            </div>
        `).join('');
    }

    refreshUnitLabels();
    refreshTypeGuide();

    inputUnit.addEventListener('change', refreshUnitLabels);
    workTypeSelect.addEventListener('change', refreshTypeGuide);

    estimationForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        clearError();

        const formData = new FormData(estimationForm);
        const payload = Object.fromEntries(formData.entries());
        const rawPayloadForSummary = { ...payload };

        const validationError = validateInputs(payload);
        if (validationError) {
            showError(validationError);
            return;
        }

        const unit = inputUnit.value;

        payload.length = toMeters(payload.length, unit);
        payload.width = toMeters(payload.width, unit);
        payload.height = toMeters(payload.height, unit);
        payload.area = toSquareMeters(payload.area, unit);

        submitSpinner.classList.remove('d-none');
        submitText.textContent = texts.calculating;

        try {
            const response = await fetch('{{ route('estimations.calculate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                let message = data.message || texts.unexpected;

                if (data.errors) {
                    const allErrors = Object.values(data.errors).flat();
                    if (allErrors.length) {
                        message = allErrors[0];
                    }
                }

                showError(message);
                return;
            }

            const result = data.data;

            resultBox.classList.remove('d-none');

            renderInputSummary(rawPayloadForSummary);

            document.getElementById('totalCost').textContent = formatNumber(result.total_cost, 2);
            document.getElementById('durationDays').textContent = formatDays(result.estimated_duration_days);

            itemsBody.innerHTML = '';
            matchesBox.innerHTML = '';

            if ((result.items || []).length === 0) {
                itemsBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            ${texts.noItems}
                        </td>
                    </tr>
                `;
            } else {
                (result.items || []).forEach(item => {
                    const materialName = isArabic
                        ? (item.material_type?.name_ar ?? item.material_type?.name_en ?? '-')
                        : (item.material_type?.name_en ?? item.material_type?.name_ar ?? '-');

                    itemsBody.innerHTML += `
                        <tr>
                            <td>${materialName}</td>
                            <td>${formatNumber(item.calculated_quantity, 3)} ${item.unit ?? ''}</td>
                            <td>${formatNumber(item.waste_quantity, 3)}</td>
                            <td>${formatNumber(item.final_quantity, 3)} ${item.unit ?? ''}</td>
                            <td>${formatNumber(item.unit_price, 2)}</td>
                            <td>${formatNumber(item.line_total, 2)}</td>
                        </tr>
                    `;
                });
            }

            if ((result.matches || []).length === 0) {
                matchesBox.innerHTML = `
                    <div class="empty-result-box">
                        ${texts.noMatches}
                    </div>
                `;
            } else {
                (result.matches || []).forEach(match => {
                    const serviceName = isArabic
                        ? (match.service?.name_ar ?? match.service?.name_en ?? 'Service')
                        : (match.service?.name_en ?? match.service?.name_ar ?? 'Service');

                    const servicePrice = match.service?.price !== null && match.service?.price !== undefined
                        ? formatNumber(match.service.price, 2)
                        : '-';

                    const serviceLink = match.service?.id
                        ? `{{ url('/services') }}/${match.service.id}`
                        : null;

                    matchesBox.innerHTML += `
                        <div class="service-card-mini">
                            <div>
                                <div class="fw-bold">
                                    ${serviceLink
                                        ? `<a href="${serviceLink}" style="color:inherit;text-decoration:none;">${serviceName}</a>`
                                        : serviceName}
                                </div>
                                <div class="text-muted small">
                                    ${formatMatchType(match.match_type)} • ${texts.score}: ${formatNumber(match.score, 0)}
                                </div>
                            </div>
                            <div class="service-price-badge">
                                ${servicePrice}
                            </div>
                        </div>
                    `;
                });
            }

            window.scrollTo({
                top: resultBox.offsetTop - 90,
                behavior: 'smooth'
            });

        } catch (error) {
            console.error(error);
            showError(texts.failed);
        } finally {
            submitSpinner.classList.add('d-none');
            submitText.textContent = texts.calcButton;
        }
    });
});
</script>

@endsection