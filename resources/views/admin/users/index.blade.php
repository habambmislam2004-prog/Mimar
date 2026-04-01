@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $selectedUser = $selectedUser ?? null;
        $roles = $roles ?? [];
    @endphp

    <style>
        .users-shell { display: grid; gap: 24px; }

        .users-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 34px;
            background: linear-gradient(135deg, #0f172a 0%, #172554 48%, #1e3a8a 100%);
            color: #fff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
        }

        .users-hero::before {
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

        .users-hero::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            top: -110px;
            inset-inline-end: -80px;
            background: radial-gradient(circle, rgba(255,255,255,.16), transparent 66%);
        }

        .users-hero-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 24px;
            align-items: end;
        }

        .users-kicker {
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

        .users-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #eab308;
        }

        .users-title {
            margin: 0 0 12px;
            font-size: 44px;
            line-height: 1.02;
            letter-spacing: -0.05em;
            font-weight: 900;
        }

        .users-copy {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .users-hero-side {
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 28px;
            padding: 20px;
            backdrop-filter: blur(12px);
        }

        .users-hero-side h3 {
            margin: 0 0 14px;
            font-size: 19px;
            font-weight: 800;
        }

        .users-hero-list {
            display: grid;
            gap: 12px;
        }

        .users-hero-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,.86);
            font-size: 14px;
        }

        .users-hero-item strong {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
        }

        .users-alert {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(5,150,105,.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,.12);
            font-size: 14px;
            font-weight: 700;
        }

        .users-stats {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 16px;
        }

        .user-stat-card {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 26px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
        }

        .user-stat-label {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .user-stat-number {
            font-size: 36px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.04em;
            margin-bottom: 6px;
        }

        .user-stat-note {
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.8;
        }

        .users-grid {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 20px;
            align-items: start;
        }

        .users-card,
        .users-form-card {
            background: #fff;
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 28px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
            padding: 24px;
        }

        .users-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .users-head h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #111827;
        }

        .users-list {
            display: grid;
            gap: 12px;
        }

        .user-item {
            display: block;
            text-decoration: none;
            padding: 16px;
            border-radius: 20px;
            background: #fafbff;
            border: 1px solid rgba(15,23,42,.06);
            transition: .2s ease;
        }

        .user-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(15,23,42,.06);
        }

        .user-item.active {
            border-color: rgba(59,130,246,.25);
            background: #eff6ff;
        }

        .user-item strong {
            display: block;
            color: #111827;
            margin-bottom: 6px;
            font-size: 16px;
        }

        .user-item span {
            display: block;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        .user-badges {
            margin-top: 10px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
        }

        .user-badge.role {
            background: rgba(37,99,235,.10);
            color: #2563eb;
        }

        .user-badge.active {
            background: rgba(5,150,105,.10);
            color: #059669;
        }

        .user-badge.inactive {
            background: rgba(239,68,68,.10);
            color: #dc2626;
        }

        .user-form {
            display: grid;
            gap: 14px;
        }

        .user-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .user-group {
            display: grid;
            gap: 8px;
        }

        .user-group.full {
            grid-column: 1 / -1;
        }

        .user-label {
            font-size: 13px;
            font-weight: 700;
            color: #374151;
        }

        .user-input,
        .user-select {
            width: 100%;
            border: 1px solid rgba(15,23,42,.08);
            background: #fff;
            border-radius: 16px;
            padding: 12px 14px;
            font-size: 14px;
            outline: none;
        }

        .user-input:focus,
        .user-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37,99,235,.10);
        }

        .user-error {
            color: #dc2626;
            font-size: 12px;
        }

        .user-help {
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
        }

        .user-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .user-btn-primary,
        .user-btn-secondary,
        .user-btn-danger {
            height: 42px;
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

        .user-btn-primary {
            background: linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);
            color: #fff;
        }

        .user-btn-secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,.08);
        }

        .user-btn-danger {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.12);
        }

        .user-preview {
            margin-top: 16px;
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid rgba(15,23,42,.06);
            background: #f8fafc;
            padding: 18px;
        }

        .user-preview strong {
            display: block;
            color: #111827;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .user-preview-meta {
            color: #64748b;
            font-size: 14px;
            line-height: 1.9;
        }

        .user-empty {
            padding: 28px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px dashed rgba(15,23,42,.10);
            color: #64748b;
            text-align: center;
        }

        @media (max-width: 1200px) {
            .users-stats {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 1100px) {
            .users-hero-content,
            .users-grid,
            .user-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .users-hero,
            .users-card,
            .users-form-card {
                padding: 20px;
                border-radius: 24px;
            }

            .users-title {
                font-size: 32px;
            }

            .users-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="users-shell">
        <section class="users-hero">
            <div class="users-hero-content">
                <div>
                    <span class="users-kicker">{{ $isArabic ? 'إدارة المستخدمين والمدراء' : 'Manage users & admins' }}</span>

                    <h1 class="users-title">
                        {{ $isArabic ? 'واجهة احترافية لإضافة مدير جديد من السوبر أدمن' : 'A premium interface for creating admins from super admin' }}
                    </h1>

                    <p class="users-copy">
                        {{ $isArabic
                            ? 'من هذه الواجهة يستطيع السوبر أدمن إضافة مدير جديد أو مستخدم جديد، وتعديل البيانات، وإدارة حالة الحسابات بشكل واضح وآمن.'
                            : 'From this interface, the super admin can create a new admin or user, update account data, and manage account statuses in a clear and secure way.' }}
                    </p>
                </div>

                <div class="users-hero-side">
                    <h3>{{ $isArabic ? 'ملخص مباشر' : 'Direct summary' }}</h3>

                    <div class="users-hero-list">
                        <div class="users-hero-item">
                            <span>{{ $isArabic ? 'إجمالي المستخدمين' : 'Total users' }}</span>
                            <strong>{{ $stats['total'] ?? 0 }}</strong>
                        </div>

                        <div class="users-hero-item">
                            <span>{{ $isArabic ? 'الحسابات المفعلة' : 'Active accounts' }}</span>
                            <strong>{{ $stats['active'] ?? 0 }}</strong>
                        </div>

                        <div class="users-hero-item">
                            <span>{{ $isArabic ? 'عدد المدراء' : 'Admins count' }}</span>
                            <strong>{{ $stats['admins'] ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="users-alert">
                {{ session('success') }}
            </div>
        @endif

        <section class="users-stats">
            <div class="user-stat-card">
                <span class="user-stat-label">{{ $isArabic ? 'إجمالي الحسابات' : 'Total accounts' }}</span>
                <div class="user-stat-number">{{ $stats['total'] ?? 0 }}</div>
                <div class="user-stat-note">{{ $isArabic ? 'كل الحسابات داخل النظام' : 'All accounts inside the system' }}</div>
            </div>

            <div class="user-stat-card">
                <span class="user-stat-label">{{ $isArabic ? 'الحسابات المفعلة' : 'Active accounts' }}</span>
                <div class="user-stat-number">{{ $stats['active'] ?? 0 }}</div>
                <div class="user-stat-note">{{ $isArabic ? 'حسابات متاحة للدخول والعمل' : 'Accounts available for login and use' }}</div>
            </div>

            <div class="user-stat-card">
                <span class="user-stat-label">{{ $isArabic ? 'الحسابات غير المفعلة' : 'Inactive accounts' }}</span>
                <div class="user-stat-number">{{ $stats['inactive'] ?? 0 }}</div>
                <div class="user-stat-note">{{ $isArabic ? 'حسابات موقوفة أو مخفية' : 'Disabled or hidden accounts' }}</div>
            </div>

            <div class="user-stat-card">
                <span class="user-stat-label">{{ $isArabic ? 'المدراء' : 'Admins' }}</span>
                <div class="user-stat-number">{{ $stats['admins'] ?? 0 }}</div>
                <div class="user-stat-note">{{ $isArabic ? 'عدد الحسابات الإدارية' : 'Administrative accounts count' }}</div>
            </div>

            <div class="user-stat-card">
                <span class="user-stat-label">{{ $isArabic ? 'المستخدمون' : 'Users' }}</span>
                <div class="user-stat-number">{{ $stats['users'] ?? 0 }}</div>
                <div class="user-stat-note">{{ $isArabic ? 'عدد الحسابات العادية' : 'Regular user accounts count' }}</div>
            </div>
        </section>

        <section class="users-grid">
            <div class="users-card">
                <div class="users-head">
                    <h2>{{ $isArabic ? 'المستخدمون' : 'Users' }}</h2>
                    <span>{{ $users->total() }}</span>
                </div>

                @if ($users->count())
                    <div class="users-list">
                        @foreach ($users as $user)
                            @php
                                $userRole = $user->getRoleNames()->first();
                            @endphp

                            <a href="{{ route('admin.users.index', ['selected' => $user->id]) }}" class="user-item {{ $selectedUser && $selectedUser->id === $user->id ? 'active' : '' }}">
                                <strong>{{ $user->name }}</strong>
                                <span>{{ $user->email ?? '—' }}</span>
                                <span>{{ $user->phone ?? '—' }}</span>

                                <div class="user-badges">
                                    <span class="user-badge role">{{ $userRole ?? '—' }}</span>

                                    <span class="user-badge {{ $user->is_active ? 'active' : 'inactive' }}">
                                        {{ $user->is_active
                                            ? ($isArabic ? 'مفعل' : 'Active')
                                            : ($isArabic ? 'غير مفعل' : 'Inactive') }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div style="margin-top:18px;">
                        {{ $users->withQueryString()->links() }}
                    </div>
                @else
                    <div class="user-empty">{{ $isArabic ? 'لا يوجد مستخدمون حالياً.' : 'No users yet.' }}</div>
                @endif
            </div>

            <div class="users-form-card">
                <div class="users-head">
                    <h2>{{ $selectedUser ? ($isArabic ? 'تعديل المستخدم' : 'Edit user') : ($isArabic ? 'إضافة مستخدم/مدير' : 'Create user/admin') }}</h2>
                </div>

                <form method="POST" action="{{ $selectedUser ? route('admin.users.update', $selectedUser->id) : route('admin.users.store') }}" class="user-form">
                    @csrf
                    @if ($selectedUser)
                        @method('PUT')
                    @endif

                    <div class="user-grid">
                        <div class="user-group">
                            <label class="user-label">{{ $isArabic ? 'الاسم' : 'Name' }}</label>
                            <input type="text" name="name" class="user-input" value="{{ old('name', $selectedUser->name ?? '') }}">
                            @error('name') <div class="user-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="user-group">
                            <label class="user-label">{{ $isArabic ? 'البريد الإلكتروني' : 'Email' }}</label>
                            <input type="email" name="email" class="user-input" value="{{ old('email', $selectedUser->email ?? '') }}">
                            @error('email') <div class="user-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="user-group">
                            <label class="user-label">{{ $isArabic ? 'رقم الهاتف' : 'Phone' }}</label>
                            <input type="text" name="phone" class="user-input" value="{{ old('phone', $selectedUser->phone ?? '') }}">
                            @error('phone') <div class="user-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="user-group">
                            <label class="user-label">{{ $isArabic ? 'الدور' : 'Role' }}</label>
                            <select name="account_type" class="user-select">
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}" @selected(old('account_type', $selectedUser->account_type ?? '') === $role)>
                                        {{ $role }}
                                    </option>
                                @endforeach
                            </select>
                            @error('account_type') <div class="user-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="user-group">
                            <label class="user-label">{{ $isArabic ? 'اللغة' : 'Locale' }}</label>
                            <select name="locale" class="user-select">
                                <option value="ar" @selected(old('locale', $selectedUser->locale ?? 'ar') === 'ar')>العربية</option>
                                <option value="en" @selected(old('locale', $selectedUser->locale ?? 'ar') === 'en')>English</option>
                            </select>
                            @error('locale') <div class="user-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="user-group">
                            <label class="user-label">{{ $isArabic ? 'الحالة' : 'Status' }}</label>
                            <select name="is_active" class="user-select">
                                <option value="1" @selected(old('is_active', $selectedUser->is_active ?? true) == 1)>{{ $isArabic ? 'مفعل' : 'Active' }}</option>
                                <option value="0" @selected(old('is_active', $selectedUser->is_active ?? true) == 0)>{{ $isArabic ? 'غير مفعل' : 'Inactive' }}</option>
                            </select>
                            @error('is_active') <div class="user-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="user-group full">
                            <label class="user-label">{{ $isArabic ? 'كلمة المرور' : 'Password' }}</label>
                            <input type="password" name="password" class="user-input" value="">
                            <div class="user-help">
                                {{ $selectedUser
                                    ? ($isArabic ? 'اتركها فارغة إذا كنت لا تريد تغيير كلمة المرور.' : 'Leave it empty if you do not want to change the password.')
                                    : ($isArabic ? 'يجب إدخال كلمة مرور عند إنشاء حساب جديد.' : 'Password is required when creating a new account.') }}
                            </div>
                            @error('password') <div class="user-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="user-actions">
                        <button type="submit" class="user-btn-primary">
                            {{ $selectedUser ? ($isArabic ? 'حفظ التعديلات' : 'Save changes') : ($isArabic ? 'إضافة الحساب' : 'Create account') }}
                        </button>

                        @if ($selectedUser)
                            <a href="{{ route('admin.users.index') }}" class="user-btn-secondary">
                                {{ $isArabic ? 'عنصر جديد' : 'New item' }}
                            </a>
                        @endif
                    </div>
                </form>

                @if ($selectedUser)
                    @php
                        $selectedRoleName = $selectedUser->getRoleNames()->first();
                        $isSuperAdmin = $selectedRoleName === \App\Enums\SystemRole::SUPER_ADMIN->value;
                        $isCurrentAuth = auth()->id() === $selectedUser->id;
                    @endphp

                    @if (! $isSuperAdmin && ! $isCurrentAuth)
                        <form method="POST" action="{{ route('admin.users.destroy', $selectedUser->id) }}" style="margin-top:14px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="user-btn-danger">
                                {{ $isArabic ? 'حذف المستخدم' : 'Delete user' }}
                            </button>
                        </form>
                    @endif

                    <div class="user-preview">
                        <strong>{{ $selectedUser->name }}</strong>
                        <div class="user-preview-meta">
                            <div>{{ $isArabic ? 'البريد:' : 'Email:' }} {{ $selectedUser->email ?? '—' }}</div>
                            <div>{{ $isArabic ? 'الهاتف:' : 'Phone:' }} {{ $selectedUser->phone ?? '—' }}</div>
                            <div>{{ $isArabic ? 'اللغة:' : 'Locale:' }} {{ $selectedUser->locale ?? '—' }}</div>
                            <div>{{ $isArabic ? 'النوع:' : 'Type:' }} {{ $selectedUser->account_type ?? '—' }}</div>
                            <div>{{ $isArabic ? 'الدور:' : 'Role:' }} {{ $selectedRoleName ?? '—' }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection