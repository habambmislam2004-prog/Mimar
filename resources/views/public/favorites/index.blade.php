@extends('layouts.app')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $favorites = $favorites ?? collect();

        $resolveServiceTitle = function ($service) use ($isArabic) {
            return $isArabic
                ? ($service->name_ar ?? $service->name_en ?? '—')
                : ($service->name_en ?? $service->name_ar ?? '—');
        };

        $resolveProviderName = function ($service) use ($isArabic) {
            if (! $service || ! $service->businessAccount) {
                return $isArabic ? 'مزود خدمة' : 'Service provider';
            }

            return $isArabic
                ? ($service->businessAccount->name_ar ?? $service->businessAccount->name_en ?? 'مزود خدمة')
                : ($service->businessAccount->name_en ?? $service->businessAccount->name_ar ?? 'Service provider');
        };

        $resolveCategoryLabel = function ($service) use ($isArabic) {
            if ($service?->subcategory) {
                return $isArabic
                    ? ($service->subcategory->name_ar ?? $service->subcategory->name_en ?? 'خدمة')
                    : ($service->subcategory->name_en ?? $service->subcategory->name_ar ?? 'Service');
            }

            if ($service?->category) {
                return $isArabic
                    ? ($service->category->name_ar ?? $service->category->name_en ?? 'خدمة')
                    : ($service->category->name_en ?? $service->category->name_ar ?? 'Service');
            }

            return $isArabic ? 'خدمة' : 'Service';
        };

        $resolveImageUrl = function ($service) {
            $image = $service?->primaryImage()?->path;

            if (! $image) {
                return 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80';
            }

            if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
                return $image;
            }

            return asset('storage/' . ltrim($image, '/'));
        };
    @endphp

    <style>
        .favorites-shell {
            display: grid;
            gap: 24px;
        }

        .favorites-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            padding: 30px;
            background:
                linear-gradient(135deg, rgba(12,18,32,.94) 0%, rgba(23,39,78,.86) 50%, rgba(39,57,101,.78) 100%);
            color: white;
            box-shadow: 0 26px 60px rgba(15,23,42,0.18);
        }

        .favorites-hero::after {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            top: -90px;
            inset-inline-end: -60px;
            background: radial-gradient(circle, rgba(255,255,255,0.14), transparent 66%);
        }

        .favorites-hero h1 {
            position: relative;
            z-index: 1;
            margin: 0 0 10px;
            font-size: 38px;
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .favorites-hero p {
            position: relative;
            z-index: 1;
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,0.84);
            font-size: 14px;
            line-height: 1.9;
        }

        .favorites-alert {
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(5,150,105,0.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,0.12);
            font-size: 14px;
            font-weight: 700;
        }

        .favorites-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .favorite-card {
            background: rgba(255,255,255,0.98);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
            transition: .2s ease;
        }

        .favorite-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(15,23,42,0.08);
        }

        .favorite-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }

        .favorite-body {
            padding: 18px;
        }

        .favorite-pill {
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

        .favorite-title {
            margin: 0 0 8px;
            font-size: 21px;
            font-weight: 800;
            color: #24304d;
            line-height: 1.3;
        }

        .favorite-text {
            margin: 0 0 14px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.85;
            min-height: 72px;
        }

        .favorite-meta {
            display: grid;
            gap: 6px;
            margin-bottom: 14px;
            color: #475569;
            font-size: 13px;
            line-height: 1.8;
        }

        .favorite-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .favorite-btn,
        .favorite-btn-ghost {
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: .2s ease;
        }

        .favorite-btn {
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: white;
            box-shadow: 0 14px 26px rgba(36,56,115,0.16);
        }

        .favorite-btn:hover {
            transform: translateY(-1px);
        }

        .favorite-btn-ghost {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,0.08);
        }

        .favorites-empty {
            padding: 34px;
            border-radius: 24px;
            background: #f8fafc;
            border: 1px dashed rgba(15,23,42,0.10);
            color: #64748b;
            font-size: 15px;
            line-height: 1.9;
            text-align: center;
        }

        @media (max-width: 1100px) {
            .favorites-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 767px) {
            .favorites-grid {
                grid-template-columns: 1fr;
            }

            .favorites-hero {
                padding: 22px;
                border-radius: 22px;
            }

            .favorites-hero h1 {
                font-size: 30px;
            }
        }
    </style>

    <div class="favorites-shell">
        <section class="favorites-hero">
            <h1>{{ $isArabic ? 'الخدمات المفضلة' : 'Favorite Services' }}</h1>
            <p>
                {{ $isArabic
                    ? 'احفظ الخدمات التي تعجبك في مكان واحد، وارجع إليها لاحقًا للمقارنة أو لإرسال طلب أو للتواصل مع مزود الخدمة.'
                    : 'Keep the services you like in one place so you can revisit them later for comparison, requests, or provider contact.' }}
            </p>
        </section>

        @if (session('success'))
            <div class="favorites-alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($favorites->count())
            <section class="favorites-grid">
                @foreach ($favorites as $favorite)
                    @php
                        $service = $favorite->service;
                    @endphp

                    @if ($service)
                        <article class="favorite-card">
                            <img src="{{ $resolveImageUrl($service) }}" alt="{{ $resolveServiceTitle($service) }}">

                            <div class="favorite-body">
                                <span class="favorite-pill">{{ $resolveCategoryLabel($service) }}</span>

                                <h3 class="favorite-title">{{ $resolveServiceTitle($service) }}</h3>

                                <p class="favorite-text">
                                    {{ \Illuminate\Support\Str::limit($service->description ?: ($isArabic ? 'لا يوجد وصف متاح حاليًا لهذه الخدمة.' : 'No description available for this service.'), 110) }}
                                </p>

                                <div class="favorite-meta">
                                    <div>
                                        <strong>{{ $isArabic ? 'مزود الخدمة:' : 'Provider:' }}</strong>
                                        {{ $resolveProviderName($service) }}
                                    </div>

                                    <div>
                                        <strong>{{ $isArabic ? 'السعر:' : 'Price:' }}</strong>
                                        {{ $service->price ?? '—' }}
                                    </div>
                                </div>

                                <div class="favorite-actions">
                                    <a href="{{ route('services.show', $service->id) }}" class="favorite-btn">
                                        {{ $isArabic ? 'عرض الخدمة' : 'View service' }}
                                    </a>

                                    <form method="POST" action="{{ route('favorites.destroy', $service->id) }}" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="favorite-btn-ghost">
                                            {{ $isArabic ? 'إزالة من المفضلة' : 'Remove favorite' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endif
                @endforeach
            </section>
        @else
            <div class="favorites-empty">
                {{ $isArabic ? 'لا توجد خدمات مفضلة لديك حاليًا.' : 'You do not have any favorite services right now.' }}
            </div>
        @endif
    </div>
@endsection