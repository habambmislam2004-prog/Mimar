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

                    <form id="estimationForm">
                        @csrf

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    {{ __('messages.city') ?? 'المدينة' }}
                                </label>
                                <select class="form-select custom-input" name="city_id" required>
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
                                <select class="form-select custom-input" name="estimation_type_id" required>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}">
                                            {{ app()->getLocale() === 'ar' ? ($type->name_ar ?? $type->name_en) : ($type->name_en ?? $type->name_ar) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    {{ __('messages.length') ?? 'الطول' }}
                                </label>
                                <input type="number" step="0.01" name="length" class="form-control custom-input" placeholder="0.00">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    {{ __('messages.width') ?? 'العرض' }}
                                </label>
                                <input type="number" step="0.01" name="width" class="form-control custom-input" placeholder="0.00">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    {{ __('messages.height') ?? 'الارتفاع' }}
                                </label>
                                <input type="number" step="0.01" name="height" class="form-control custom-input" placeholder="0.00">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    {{ __('messages.ready_area') ?? 'المساحة الجاهزة (اختياري)' }}
                                </label>
                                <input type="number" step="0.01" name="area" class="form-control custom-input" placeholder="0.00">
                            </div>

                            <div class="form-group form-group-full">
                                <label class="form-label">
                                    {{ __('messages.coats') ?? 'عدد طبقات الدهان' }}
                                </label>
                                <input type="number" name="coats" min="1" max="5" value="2" class="form-control custom-input">
                            </div>
                        </div>

                        <div class="form-footer">
                            <div class="form-note">
                                {{ app()->getLocale() === 'ar'
                                    ? 'هذا التقدير أولي ويعتمد على البيانات والأسعار المتوفرة.'
                                    : 'This estimate is preliminary and depends on available data and prices.' }}
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
                                <p>{{ app()->getLocale() === 'ar' ? 'اختيار المدينة ونوع العمل وإدخال الأبعاد.' : 'Choose city, work type, and enter dimensions.' }}</p>
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
                                <p>{{ app()->getLocale() === 'ar' ? 'إظهار المواد المطلوبة والخدمات المقترحة.' : 'Show required materials and suggested services.' }}</p>
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
        display: block;
        margin-bottom: 8px;
        color: #1f2937;
        font-size: 13px;
        font-weight: 700;
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
        max-width: 420px;
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

    @media (max-width: 1199px) {
        .estimation-hero,
        .estimation-grid {
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
document.getElementById('estimationForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const payload = Object.fromEntries(formData.entries());

    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    const resultBox = document.getElementById('resultBox');
    const itemsBody = document.getElementById('itemsTableBody');
    const matchesBox = document.getElementById('serviceMatches');

    submitSpinner.classList.remove('d-none');
    submitText.textContent = '{{ __("messages.calculating") ?? "جاري الحساب..." }}';

    try {
        const response = await fetch('/api/v1/estimations/calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer {{ $token ?? "" }}',
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (!data.success) {
            alert(data.message || '{{ __("messages.unexpected_error") ?? "حدث خطأ غير متوقع" }}');
            return;
        }

        const result = data.data;

        resultBox.classList.remove('d-none');

        document.getElementById('totalCost').textContent = result.total_cost ?? 0;
        document.getElementById('durationDays').textContent =
            ((result.estimated_duration_days ?? '-') + ' {{ __("messages.days") ?? "يوم" }}');

        itemsBody.innerHTML = '';
        matchesBox.innerHTML = '';

        (result.items || []).forEach(item => {
            const materialName = '{{ app()->getLocale() }}' === 'ar'
                ? (item.material_type?.name_ar ?? '-')
                : (item.material_type?.name_en ?? item.material_type?.name_ar ?? '-');

            itemsBody.innerHTML += `
                <tr>
                    <td>${materialName}</td>
                    <td>${item.calculated_quantity ?? '-'} ${item.unit ?? ''}</td>
                    <td>${item.waste_quantity ?? '-'}</td>
                    <td>${item.final_quantity ?? '-'}</td>
                    <td>${item.unit_price ?? '-'}</td>
                    <td>${item.line_total ?? '-'}</td>
                </tr>
            `;
        });

        (result.matches || []).forEach(match => {
            const serviceName = '{{ app()->getLocale() }}' === 'ar'
                ? (match.service?.name_ar ?? match.service?.name_en ?? 'Service')
                : (match.service?.name_en ?? match.service?.name_ar ?? 'Service');

            matchesBox.innerHTML += `
                <div class="service-card-mini">
                    <div>
                        <div class="fw-bold">${serviceName}</div>
                        <div class="text-muted small">${match.match_reason ?? ''}</div>
                    </div>
                    <div class="service-price-badge">
                        ${match.service?.price ?? '-'}
                    </div>
                </div>
            `;
        });

        window.scrollTo({
            top: resultBox.offsetTop - 90,
            behavior: 'smooth'
        });

    } catch (error) {
        console.error(error);
        alert('{{ __("messages.failed_connection") ?? "فشل الاتصال مع الخادم" }}');
    } finally {
        submitSpinner.classList.add('d-none');
        submitText.textContent = '{{ __("messages.calculate_estimation") ?? "احسب التقدير" }}';
    }
});
</script>

@endsection