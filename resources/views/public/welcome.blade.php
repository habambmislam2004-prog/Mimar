@extends('layouts.marketing')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $switchLocale = $isArabic ? 'en' : 'ar';
    @endphp

    <style>
        .preview-page {
            padding: 20px;
        }

        .preview-toolbar {
            max-width: 1480px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .preview-title {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #1e293b;
        }

        .preview-subtitle {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        .preview-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .device-select,
        .lang-btn {
            height: 44px;
            border-radius: 999px;
            border: 1px solid rgba(15,23,42,0.08);
            background: rgba(255,255,255,0.95);
            padding: 0 16px;
            font-size: 14px;
            font-weight: 700;
            color: #1f2f4d;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(15,23,42,0.04);
        }

        .preview-stage {
            max-width: 1480px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
        }

        .device-frame {
            width: 100%;
            transition: max-width .3s ease;
        }

        .device-frame[data-device="desktop-large"] { max-width: 1440px; }
        .device-frame[data-device="laptop"] { max-width: 1280px; }
        .device-frame[data-device="ipad-pro"] { max-width: 1024px; }
        .device-frame[data-device="ipad-mini"] { max-width: 768px; }
        .device-frame[data-device="surface-pro-7"] { max-width: 912px; }
        .device-frame[data-device="iphone-se"] { max-width: 375px; }
        .device-frame[data-device="iphone-17-pro"] { max-width: 390px; }
        .device-frame[data-device="iphone-14-pro-max"] { max-width: 430px; }
        .device-frame[data-device="pixel-7"] { max-width: 412px; }
        .device-frame[data-device="galaxy-s25-ultra"] { max-width: 412px; }
        .device-frame[data-device="galaxy-z-fold-5"] { max-width: 344px; }

        .device-screen {
            background: rgba(255,255,255,0.32);
            border: 1px solid rgba(255,255,255,0.45);
            backdrop-filter: blur(16px);
            border-radius: 34px;
            box-shadow: 0 30px 80px rgba(15,23,42,0.14);
            overflow: hidden;
        }

        .landing {
            background: #f5f7fb;
        }

        .landing-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 24px;
            background: rgba(255,255,255,0.96);
            border-bottom: 1px solid rgba(15,23,42,0.06);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 18px;
        }

        .brand-name {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            color: #24304d;
            letter-spacing: -0.03em;
        }

        .brand-sub {
            margin: 2px 0 0;
            color: #64748b;
            font-size: 12px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .header-register {
            height: 42px;
            padding: 0 18px;
            border-radius: 999px;
            background: #f8fafc;
            color: #334155;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            border: 1px solid rgba(15,23,42,0.08);
        }

        .header-login {
            height: 42px;
            padding: 0 18px;
            border-radius: 999px;
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 12px 24px rgba(36,56,115,0.18);
        }

        .hero {
            position: relative;
            min-height: 560px;
            background:
                linear-gradient(rgba(9,15,28,0.24), rgba(9,15,28,0.44)),
                url('https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
            overflow: hidden;
        }

        .hero-center-card {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: min(88%, 700px);
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.18);
            backdrop-filter: blur(16px);
            border-radius: 30px;
            padding: 30px 28px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(15,23,42,0.22);
            color: white;
        }

        .hero-center-card h2 {
            margin: 0 0 8px;
            font-size: 52px;
            line-height: 1.06;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .hero-center-card p {
            margin: 0;
            color: rgba(255,255,255,0.92);
            font-size: 15px;
            line-height: 1.9;
            max-width: 620px;
            margin-inline: auto;
        }

        .hero-buttons {
            margin-top: 18px;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .hero-btn-primary,
        .hero-btn-secondary {
            min-width: 150px;
            height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
        }

        .hero-btn-primary {
            background: white;
            color: #243873;
        }

        .hero-btn-secondary {
            background: rgba(255,255,255,0.12);
            color: white;
            border: 1px solid rgba(255,255,255,0.18);
        }

        .hero-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: rgba(17,24,39,0.28);
            border: 1px solid rgba(255,255,255,0.14);
            color: white;
            display: grid;
            place-items: center;
            font-size: 22px;
            backdrop-filter: blur(8px);
        }

        .hero-arrow.prev { inset-inline-start: 18px; }
        .hero-arrow.next { inset-inline-end: 18px; }

        .hero-dots {
            position: absolute;
            bottom: 18px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
        }

        .hero-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255,255,255,0.48);
        }

        .hero-dot.active {
            background: white;
        }

        .landing-body {
            padding: 28px 24px 32px;
        }

        .stats-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .stat-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15,23,42,0.05);
        }

        .stat-number {
            font-size: 30px;
            font-weight: 800;
            color: #243873;
            margin-bottom: 6px;
        }

        .stat-label {
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }

        .grid-two {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 20px;
            align-items: stretch;
        }

        .card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
        }

        .section-heading {
            margin: 0 0 14px;
            font-size: 34px;
            line-height: 1.08;
            font-weight: 800;
            color: #24304d;
            letter-spacing: -0.03em;
        }

        .section-copy {
            margin: 0;
            color: #64748b;
            font-size: 15px;
            line-height: 2;
        }

        .why-title {
            margin: 0 0 14px;
            font-size: 24px;
            font-weight: 800;
            color: #24304d;
        }

        .why-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 12px;
        }

        .why-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            color: #334155;
            line-height: 1.8;
            font-size: 15px;
        }

        .why-list li::before {
            content: "✓";
            color: #4458db;
            font-weight: 800;
            margin-top: 2px;
        }

        .services-section {
            margin-top: 24px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .service-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(15,23,42,0.05);
        }

        .service-card img {
            width: 100%;
            height: 190px;
            object-fit: cover;
            display: block;
        }

        .service-content {
            padding: 18px;
        }

        .service-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(68,88,219,0.08);
            color: #4458db;
            font-size: 12px;
            font-weight: 700;
        }

        .service-title {
            margin: 0 0 8px;
            font-size: 20px;
            font-weight: 800;
            color: #1f2f4d;
        }

        .service-text {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.8;
        }

        .cta-band {
            margin-top: 24px;
            border-radius: 28px;
            padding: 30px;
            background: linear-gradient(135deg, #182848 0%, #243b73 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            box-shadow: 0 24px 54px rgba(24,40,72,0.22);
        }

        .cta-band h3 {
            margin: 0 0 10px;
            font-size: 30px;
            line-height: 1.12;
            font-weight: 800;
        }

        .cta-band p {
            margin: 0;
            color: rgba(255,255,255,0.82);
            font-size: 15px;
            line-height: 1.9;
            max-width: 700px;
        }

        .cta-band a {
            min-width: 170px;
            height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            background: white;
            color: #243873;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            white-space: nowrap;
        }

        .footer {
            margin-top: 28px;
            padding-top: 18px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .footer h4 {
            margin: 0 0 12px;
            font-size: 18px;
            font-weight: 800;
            color: #1f2f4d;
        }

        .footer p,
        .footer a {
            display: block;
            margin: 0 0 8px;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            line-height: 1.8;
        }

        @media (max-width: 1200px) {
            .services-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .stats-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1023px) {
            .hero {
                min-height: 380px;
            }

            .hero-center-card h2 {
                font-size: 36px;
            }

            .grid-two,
            .services-grid,
            .footer {
                grid-template-columns: 1fr;
            }

            .cta-band {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 767px) {
            .preview-page {
                padding: 12px;
            }

            .device-screen {
                border-radius: 24px;
            }

            .landing-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 14px 16px;
            }

            .header-actions {
                width: 100%;
                justify-content: space-between;
            }

            .hero {
                min-height: 280px;
            }

            .hero-center-card {
                width: calc(100% - 24px);
                padding: 18px 16px;
                border-radius: 20px;
            }

            .hero-center-card h2 {
                font-size: 26px;
                line-height: 1.18;
            }

            .hero-center-card p {
                font-size: 13px;
            }

            .hero-buttons {
                margin-top: 14px;
            }

            .hero-btn-primary,
            .hero-btn-secondary {
                width: 100%;
            }

            .landing-body {
                padding: 18px 16px 24px;
            }

            .section-heading {
                font-size: 28px;
            }

            .card {
                padding: 20px;
                border-radius: 20px;
            }

            .service-card img {
                height: 160px;
            }

            .cta-band h3 {
                font-size: 24px;
            }

            .stats-strip {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="preview-page">
        <div class="preview-toolbar">
            <div>
                <h1 class="preview-title">Mi'mar Premium Preview</h1>
                <p class="preview-subtitle">
                    {{ app()->getLocale() === 'ar'
                        ? 'اختر الجهاز لمعاينة الاستجابة على أحجام متعددة'
                        : 'Choose a device to preview the responsive experience across multiple sizes' }}
                </p>
            </div>

            <div class="preview-controls">
                <select class="device-select" id="deviceSelect">
                    <option value="desktop-large">Desktop Large</option>
                    <option value="laptop">Laptop</option>
                    <option value="ipad-pro">iPad Pro</option>
                    <option value="ipad-mini">iPad Mini</option>
                    <option value="surface-pro-7">Surface Pro 7</option>
                    <option value="iphone-se">iPhone SE</option>
                    <option value="iphone-17-pro">iPhone 17 Pro</option>
                    <option value="iphone-14-pro-max">iPhone 14 Pro Max</option>
                    <option value="pixel-7">Pixel 7</option>
                    <option value="galaxy-s25-ultra">Galaxy S25 Ultra</option>
                    <option value="galaxy-z-fold-5">Galaxy Z Fold 5</option>
                </select>

                <a class="lang-btn" href="{{ route('lang.switch', $switchLocale) }}">
                    {{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}
                </a>
            </div>
        </div>

        <div class="preview-stage">
            <div class="device-frame" id="deviceFrame" data-device="desktop-large">
                <div class="device-screen">
                    <div class="landing">
                        <header class="landing-header">
                            <a href="{{ route('welcome') }}" class="brand">
                                <div class="brand-mark">M</div>
                                <div>
                                    <h2 class="brand-name">Mi'mar</h2>
                                    <p class="brand-sub">
                                        {{ app()->getLocale() === 'ar'
                                            ? 'منصة الخدمات العقارية والتقدير الذكي'
                                            : 'Real Estate Services & Smart Estimation Platform' }}
                                    </p>
                                </div>
                            </a>

                            <div class="header-actions">
                                <a href="{{ route('register') }}" class="header-register">
                                    {{ app()->getLocale() === 'ar' ? 'إنشاء حساب' : 'Register' }}
                                </a>

                                <a href="{{ route('login') }}" class="header-login">
                                    {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Login' }}
                                </a>
                            </div>
                        </header>

                        <section class="hero">
                            <div class="hero-arrow prev">‹</div>
                            <div class="hero-arrow next">›</div>

                            <div class="hero-center-card">
                                <h2>
                                    {{ app()->getLocale() === 'ar'
                                        ? 'اكتشف خدماتك العقارية الذكية وابدأ بثقة'
                                        : 'Discover smart real estate services and start with confidence' }}
                                </h2>

                                <p>
                                    {{ app()->getLocale() === 'ar'
                                        ? 'استعرض الخدمات، قارن الخيارات، وابدأ تقدير مشروعك قبل التنفيذ ضمن تجربة رقمية فخمة وواضحة.'
                                        : 'Browse services, compare options, and estimate your project before execution through a premium digital experience.' }}
                                </p>

                                <div class="hero-buttons">
                                    <a href="{{ route('register') }}" class="hero-btn-primary">
                                        {{ app()->getLocale() === 'ar' ? 'ابدأ الآن' : 'Get started' }}
                                    </a>

                                    <a href="{{ route('estimations.create') }}" class="hero-btn-secondary">
                                        {{ app()->getLocale() === 'ar' ? 'التقدير الذكي' : 'Smart estimation' }}
                                    </a>
                                </div>
                            </div>

                            <div class="hero-dots">
                                <div class="hero-dot"></div>
                                <div class="hero-dot active"></div>
                                <div class="hero-dot"></div>
                            </div>
                        </section>

                        <div class="landing-body">
                            <section class="stats-strip">
                                <div class="stat-card">
                                    <div class="stat-number">120+</div>
                                    <div class="stat-label">{{ app()->getLocale() === 'ar' ? 'خدمة قابلة للإدارة داخل المنصة' : 'Managed service options' }}</div>
                                </div>

                                <div class="stat-card">
                                    <div class="stat-number">35+</div>
                                    <div class="stat-label">{{ app()->getLocale() === 'ar' ? 'حساب أعمال ومزود خدمة' : 'Business accounts & providers' }}</div>
                                </div>

                                <div class="stat-card">
                                    <div class="stat-number">08</div>
                                    <div class="stat-label">{{ app()->getLocale() === 'ar' ? 'تصنيفات تشغيلية رئيسية' : 'Main service categories' }}</div>
                                </div>

                                <div class="stat-card">
                                    <div class="stat-number">24/7</div>
                                    <div class="stat-label">{{ app()->getLocale() === 'ar' ? 'وصول رقمي للخدمات والمحادثات' : 'Digital access to services and messaging' }}</div>
                                </div>
                            </section>

                            <section class="grid-two">
                                <div class="card">
                                    <h3 class="section-heading">
                                        {{ app()->getLocale() === 'ar' ? 'من نحن' : 'About us' }}
                                    </h3>

                                    <p class="section-copy">
                                        {{ app()->getLocale() === 'ar'
                                            ? 'Mi\'mar منصة عقارية حديثة تربط المستخدمين بمقدمي الخدمات، وتمنحهم تجربة عملية تبدأ من الاكتشاف والمقارنة، وتمر بالطلبات والمحادثات، وتنتهي بإمكانية تقدير أولي للكميات والتكلفة قبل التنفيذ.'
                                            : 'Mi\'mar is a modern real estate service platform that helps users discover, compare, request, communicate, and estimate quantities/cost before execution.' }}
                                    </p>
                                </div>

                                <div class="card" style="background:#eef4ff;">
                                    <h4 class="why-title">
                                        {{ app()->getLocale() === 'ar' ? 'لماذا Mi\'mar؟' : 'Why choose Mi\'mar?' }}
                                    </h4>

                                    <ul class="why-list">
                                        <li>{{ app()->getLocale() === 'ar' ? 'واجهة أنيقة ومتجاوبة على مختلف الأجهزة' : 'Elegant and responsive across multiple devices' }}</li>
                                        <li>{{ app()->getLocale() === 'ar' ? 'خدمات، طلبات، ومحادثات ضمن تدفق واحد' : 'Services, requests, and chat in one flow' }}</li>
                                        <li>{{ app()->getLocale() === 'ar' ? 'تقدير ذكي لدعم القرار قبل التنفيذ' : 'Smart estimation before execution' }}</li>
                                        <li>{{ app()->getLocale() === 'ar' ? 'مخصص للمشاريع العقارية الصغيرة والمتوسطة' : 'Designed for small and medium real estate projects' }}</li>
                                    </ul>
                                </div>
                            </section>

                            <section class="services-section">
                                <h3 class="section-heading">
                                    {{ app()->getLocale() === 'ar' ? 'الخدمات المميزة' : 'Featured services' }}
                                </h3>

                                <div class="services-grid">
                                    <article class="service-card">
                                        <img src="https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80" alt="service">
                                        <div class="service-content">
                                            <span class="service-pill">{{ app()->getLocale() === 'ar' ? 'تشطيبات' : 'Finishing' }}</span>
                                            <h4 class="service-title">{{ app()->getLocale() === 'ar' ? 'التشطيبات الداخلية' : 'Interior finishing' }}</h4>
                                            <p class="service-text">{{ app()->getLocale() === 'ar' ? 'تنفيذ تشطيبات احترافية للشقق والمشاريع السكنية الصغيرة.' : 'Professional interior finishing for apartments and smaller residential projects.' }}</p>
                                        </div>
                                    </article>

                                    <article class="service-card">
                                        <img src="https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80" alt="service">
                                        <div class="service-content">
                                            <span class="service-pill">{{ app()->getLocale() === 'ar' ? 'دهان' : 'Painting' }}</span>
                                            <h4 class="service-title">{{ app()->getLocale() === 'ar' ? 'أعمال الدهان' : 'Painting works' }}</h4>
                                            <p class="service-text">{{ app()->getLocale() === 'ar' ? 'خدمات دهان منظمة بجودة واضحة وتنفيذ نظيف.' : 'Organized painting services with clean execution and quality results.' }}</p>
                                        </div>
                                    </article>

                                    <article class="service-card">
                                        <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80" alt="service">
                                        <div class="service-content">
                                            <span class="service-pill">{{ app()->getLocale() === 'ar' ? 'بناء' : 'Construction' }}</span>
                                            <h4 class="service-title">{{ app()->getLocale() === 'ar' ? 'البناء الخفيف والترميم' : 'Light construction & repair' }}</h4>
                                            <p class="service-text">{{ app()->getLocale() === 'ar' ? 'حلول مناسبة لأعمال الجدران والترميم والتجهيز.' : 'Suitable solutions for walls, repair works, and project setup.' }}</p>
                                        </div>
                                    </article>

                                    <article class="service-card">
                                        <img src="https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80" alt="service">
                                        <div class="service-content">
                                            <span class="service-pill">{{ app()->getLocale() === 'ar' ? 'تصميم' : 'Design' }}</span>
                                            <h4 class="service-title">{{ app()->getLocale() === 'ar' ? 'استشارات وتصميم' : 'Consultation & design' }}</h4>
                                            <p class="service-text">{{ app()->getLocale() === 'ar' ? 'إرشاد أولي وخيارات تصميمية للمساحات السكنية.' : 'Early consultation and design ideas for residential spaces.' }}</p>
                                        </div>
                                    </article>
                                </div>
                            </section>

                            <section class="cta-band">
                                <div>
                                    <h3>{{ app()->getLocale() === 'ar' ? 'ابدأ بتقدير مشروعك قبل أي خطوة تنفيذية' : 'Estimate your project before any execution step' }}</h3>
                                    <p>
                                        {{ app()->getLocale() === 'ar'
                                            ? 'موديول التقدير الذكي في Mi\'mar يساعدك على حساب الكميات والتكلفة التقريبية وربط النتيجة مباشرة بالخدمات المناسبة.'
                                            : 'Mi\'mar’s smart estimation module helps you calculate quantities and estimated cost, then connect the result directly with matching services.' }}
                                    </p>
                                </div>

                                <a href="{{ route('estimations.create') }}">
                                    {{ app()->getLocale() === 'ar' ? 'ابدأ التقدير' : 'Start estimation' }}
                                </a>
                            </section>

                            <footer class="footer">
                                <div>
                                    <h4>Mi'mar</h4>
                                    <p>{{ app()->getLocale() === 'ar' ? 'منصة خدمات عقارية وتقدير ذكي للمشاريع الصغيرة والمتوسطة.' : 'A real estate services and smart estimation platform for small and medium projects.' }}</p>
                                </div>

                                <div>
                                    <h4>{{ app()->getLocale() === 'ar' ? 'استكشف' : 'Explore' }}</h4>
                                    <a href="{{ route('welcome') }}">{{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home' }}</a>
                                    <a href="{{ route('login') }}">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Login' }}</a>
                                    <a href="{{ route('register') }}">{{ app()->getLocale() === 'ar' ? 'إنشاء حساب' : 'Register' }}</a>
                                </div>

                                <div>
                                    <h4>{{ app()->getLocale() === 'ar' ? 'الدعم' : 'Support' }}</h4>
                                    <p>{{ app()->getLocale() === 'ar' ? 'الطلبات' : 'Orders' }}</p>
                                    <p>{{ app()->getLocale() === 'ar' ? 'المحادثات' : 'Conversations' }}</p>
                                    <p>{{ app()->getLocale() === 'ar' ? 'التقدير الذكي' : 'Smart estimation' }}</p>
                                </div>

                                <div>
                                    <h4>{{ app()->getLocale() === 'ar' ? 'تواصل' : 'Get in touch' }}</h4>
                                    <p>info@mimar.test</p>
                                    <p>+963 000 000 000</p>
                                    <p>{{ app()->getLocale() === 'ar' ? 'سوريا' : 'Syria' }}</p>
                                </div>
                            </footer>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <script>
    const deviceSelect = document.getElementById('deviceSelect');
    const deviceFrame = document.getElementById('deviceFrame');

    const savedDevice = localStorage.getItem('mimar-preview-device');
    if (savedDevice && deviceSelect && deviceFrame) {
        deviceSelect.value = savedDevice;
        deviceFrame.setAttribute('data-device', savedDevice);
    }

    if (deviceSelect) {
        deviceSelect.addEventListener('change', function () {
            localStorage.setItem('mimar-preview-device', this.value);
            if (deviceFrame) {
                deviceFrame.setAttribute('data-device', this.value);
            }
        });
    }
</script>
@endsection