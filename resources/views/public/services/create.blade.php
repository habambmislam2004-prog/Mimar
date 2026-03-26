@extends('layouts.app')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
    @endphp

    <style>
        .service-form-shell {
            display: grid;
            gap: 22px;
        }

        .service-form-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            background:
                linear-gradient(135deg, rgba(12,18,32,.92) 0%, rgba(20,32,61,.80) 45%, rgba(34,52,95,.72) 100%),
                radial-gradient(circle at top right, rgba(255,255,255,0.10), transparent 24%);
            color: white;
            padding: 30px;
            box-shadow: 0 26px 60px rgba(15,23,42,0.18);
        }

        .service-form-hero h1 {
            margin: 0 0 10px;
            font-size: 40px;
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .service-form-hero p {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,0.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .service-form-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 28px;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
            padding: 24px;
        }

        .service-form-title {
            margin: 0 0 8px;
            font-size: 28px;
            line-height: 1.08;
            font-weight: 800;
            color: #24304d;
        }

        .service-form-subtitle {
            margin: 0 0 20px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.9;
        }

        .service-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .service-form-group {
            display: grid;
            gap: 8px;
        }

        .service-form-group.full {
            grid-column: 1 / -1;
        }

        .service-label {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        .service-input,
        .service-select,
        .service-textarea,
        .service-file {
            width: 100%;
            border: 1px solid rgba(15,23,42,0.08);
            background: #fff;
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .service-textarea {
            min-height: 140px;
            resize: vertical;
        }

        .service-input:focus,
        .service-select:focus,
        .service-textarea:focus {
            border-color: #4458db;
            box-shadow: 0 0 0 4px rgba(68,88,219,0.10);
        }

        .service-file {
            border-style: dashed;
        }

        .service-help {
            color: #64748b;
            font-size: 12px;
            line-height: 1.8;
            margin-top: 4px;
        }

        .service-error {
            color: #dc2626;
            font-size: 12px;
        }

        .service-alert {
            background: rgba(239,68,68,0.08);
            color: #b91c1c;
            border: 1px solid rgba(239,68,68,0.12);
            border-radius: 18px;
            padding: 14px 16px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .service-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .service-btn-primary,
        .service-btn-secondary {
            height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: 800;
            border: none;
            cursor: pointer;
        }

        .service-btn-primary {
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: white;
            box-shadow: 0 14px 26px rgba(36,56,115,0.16);
        }

        .service-btn-secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,0.08);
        }

        @media (max-width: 767px) {
            .service-form-grid {
                grid-template-columns: 1fr;
            }

            .service-form-hero h1 {
                font-size: 30px;
            }
        }
    </style>

    <div class="service-form-shell">
        <section class="service-form-hero">
            <h1>{{ $isArabic ? 'إضافة خدمة جديدة' : 'Create a new service' }}</h1>
            <p>
                {{ $isArabic
                    ? 'أدخل بيانات الخدمة وارفع صورها لتظهر ضمن المنصة بشكل احترافي وواضح.'
                    : 'Enter the service details and upload its images so it appears on the platform in a clear, professional way.' }}
            </p>
        </section>

        <section class="service-form-card">
            <h2 class="service-form-title">{{ $isArabic ? 'بيانات الخدمة' : 'Service information' }}</h2>
            <p class="service-form-subtitle">
                {{ $isArabic
                    ? 'املأ الحقول الأساسية ثم احفظ الخدمة. يمكنك لاحقًا تعديلها أو إضافة صور أكثر.'
                    : 'Fill in the core fields, then save the service. You can later edit it or add more images.' }}
            </p>

            @if ($errors->any())
                <div class="service-alert">
                    {{ $isArabic ? 'يرجى مراجعة الحقول وإصلاح الأخطاء الظاهرة أدناه.' : 'Please review the fields and fix the errors shown below.' }}
                </div>
            @endif

            <form method="POST" action="{{ route('services.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="service-form-grid">
                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'حساب الأعمال' : 'Business account' }}</label>
                        <select class="service-select" name="business_account_id">
                            <option value="">{{ $isArabic ? 'اختر حساب الأعمال' : 'Select business account' }}</option>
                            @foreach ($businessAccounts as $businessAccount)
                                <option value="{{ $businessAccount->id }}" @selected(old('business_account_id') == $businessAccount->id)>
                                    {{ $isArabic
                                        ? ($businessAccount->name_ar ?? $businessAccount->name_en ?? ('#' . $businessAccount->id))
                                        : ($businessAccount->name_en ?? $businessAccount->name_ar ?? ('#' . $businessAccount->id)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('business_account_id') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'التصنيف' : 'Category' }}</label>
                        <select class="service-select" name="category_id">
                            <option value="">{{ $isArabic ? 'اختر التصنيف' : 'Select category' }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $isArabic
                                        ? ($category->name_ar ?? $category->name_en ?? ('#' . $category->id))
                                        : ($category->name_en ?? $category->name_ar ?? ('#' . $category->id)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'التصنيف الفرعي' : 'Subcategory' }}</label>
                        <select class="service-select" name="subcategory_id">
                            <option value="">{{ $isArabic ? 'اختر التصنيف الفرعي' : 'Select subcategory' }}</option>
                            @foreach ($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}" @selected(old('subcategory_id') == $subcategory->id)>
                                    {{ $isArabic
                                        ? ($subcategory->name_ar ?? $subcategory->name_en ?? ('#' . $subcategory->id))
                                        : ($subcategory->name_en ?? $subcategory->name_ar ?? ('#' . $subcategory->id)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('subcategory_id') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'السعر' : 'Price' }}</label>
                        <input class="service-input" type="number" step="0.01" name="price" value="{{ old('price') }}">
                        @error('price') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'اسم الخدمة بالعربية' : 'Service name (Arabic)' }}</label>
                        <input class="service-input" type="text" name="name_ar" value="{{ old('name_ar') }}">
                        @error('name_ar') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'اسم الخدمة بالإنجليزية' : 'Service name (English)' }}</label>
                        <input class="service-input" type="text" name="name_en" value="{{ old('name_en') }}">
                        @error('name_en') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group full">
                        <label class="service-label">{{ $isArabic ? 'الوصف' : 'Description' }}</label>
                        <textarea class="service-textarea" name="description">{{ old('description') }}</textarea>
                        @error('description') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group full">
                        <label class="service-label">{{ $isArabic ? 'صور الخدمة' : 'Service images' }}</label>
                        <input class="service-file" type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp">
                        <div class="service-help">
                            {{ $isArabic ? 'يمكنك رفع أكثر من صورة، وستُستخدم الأولى كصورة رئيسية.' : 'You can upload multiple images, and the first one will be used as the primary image.' }}
                        </div>
                        @error('images') <div class="service-error">{{ $message }}</div> @enderror
                        @error('images.*') <div class="service-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="service-actions">
                    <button type="submit" class="service-btn-primary">
                        {{ $isArabic ? 'حفظ الخدمة' : 'Save service' }}
                    </button>

                    <a href="{{ route('services.index') }}" class="service-btn-secondary">
                        {{ $isArabic ? 'العودة للخدمات' : 'Back to services' }}
                    </a>
                </div>
            </form>
        </section>
    </div>
@endsection