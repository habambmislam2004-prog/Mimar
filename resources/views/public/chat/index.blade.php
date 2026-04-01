@extends('layouts.app')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $conversations = $conversations ?? collect();
        $selectedConversation = $selectedConversation ?? null;
        $otherUsers = $otherUsers ?? collect();
        $authId = auth()->id();

        $otherParty = function ($conversation) use ($authId) {
            if (! $conversation) {
                return null;
            }

            return $conversation->user_one_id === $authId
                ? $conversation->userTwo
                : $conversation->userOne;
        };
    @endphp

    <style>
        .chat-shell { display: grid; gap: 24px; }

        .chat-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 34px;
            background: linear-gradient(135deg, #0f172a 0%, #172554 48%, #1e3a8a 100%);
            color: #fff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
        }

        .chat-hero::before {
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

        .chat-hero-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 24px;
            align-items: end;
        }

        .chat-kicker {
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

        .chat-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #eab308;
        }

        .chat-title {
            margin: 0 0 12px;
            font-size: 42px;
            line-height: 1.02;
            letter-spacing: -0.05em;
            font-weight: 900;
        }

        .chat-copy {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .chat-hero-side {
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 28px;
            padding: 20px;
            backdrop-filter: blur(12px);
        }

        .chat-hero-side h3 {
            margin: 0 0 14px;
            font-size: 19px;
            font-weight: 800;
        }

        .chat-hero-list {
            display: grid;
            gap: 12px;
        }

        .chat-hero-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,.86);
            font-size: 14px;
        }

        .chat-hero-item strong {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
        }

        .chat-layout {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 20px;
            align-items: start;
        }

        .chat-card {
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 30px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
            padding: 24px;
        }

        .chat-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .chat-head h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            color: #0f172a;
        }

        .chat-form {
            display: grid;
            gap: 12px;
            margin-bottom: 20px;
        }

        .chat-select,
        .chat-input,
        .chat-textarea {
            width: 100%;
            border: 1px solid rgba(15,23,42,.08);
            background: #fff;
            border-radius: 16px;
            padding: 13px 14px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .chat-textarea {
            min-height: 110px;
            resize: vertical;
        }

        .chat-select:focus,
        .chat-input:focus,
        .chat-textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.10);
        }

        .chat-btn {
            height: 44px;
            padding: 0 16px;
            border-radius: 999px;
            background: linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
            border: none;
            cursor: pointer;
        }

        .chat-list {
            display: grid;
            gap: 12px;
        }

        .chat-item {
            display: block;
            text-decoration: none;
            padding: 16px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,.06);
            transition: .2s ease;
        }

        .chat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(15,23,42,.06);
        }

        .chat-item.active {
            border-color: rgba(59,130,246,.25);
            background: #eff6ff;
        }

        .chat-item strong {
            display: block;
            color: #111827;
            margin-bottom: 6px;
            font-size: 16px;
        }

        .chat-item span {
            display: block;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        .message-list {
            display: grid;
            gap: 12px;
            max-height: 520px;
            overflow: auto;
            padding-right: 4px;
        }

        .message-bubble {
            max-width: 78%;
            padding: 14px 16px;
            border-radius: 20px;
            font-size: 14px;
            line-height: 1.9;
        }

        .message-bubble.mine {
            margin-inline-start: auto;
            background: linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);
            color: #fff;
            border-bottom-right-radius: 8px;
        }

        .message-bubble.other {
            margin-inline-end: auto;
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,.06);
            border-bottom-left-radius: 8px;
        }

        .message-meta {
            margin-top: 6px;
            font-size: 11px;
            opacity: .8;
        }

        .chat-empty {
            padding: 28px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px dashed rgba(15,23,42,.10);
            color: #64748b;
            text-align: center;
        }

        @media (max-width: 1100px) {
            .chat-hero-content,
            .chat-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .chat-hero,
            .chat-card {
                padding: 20px;
                border-radius: 24px;
            }

            .chat-title {
                font-size: 32px;
            }

            .message-bubble {
                max-width: 100%;
            }
        }
    </style>

    <div class="chat-shell">
        <section class="chat-hero">
            <div class="chat-hero-content">
                <div>
                    <span class="chat-kicker">{{ $isArabic ? 'المحادثات' : 'Conversations' }}</span>
                    <h1 class="chat-title">{{ $isArabic ? 'محادثاتك داخل المنصة' : 'Your conversations on the platform' }}</h1>
                    <p class="chat-copy">
                        {{ $isArabic
                            ? 'أنشئ محادثة جديدة، تابع الرسائل، وواصل التواصل مع المستخدمين داخل واجهة أوضح وأرتب.'
                            : 'Create a new conversation, review messages, and keep communication flowing in a cleaner interface.' }}
                    </p>
                </div>

                <div class="chat-hero-side">
                    <h3>{{ $isArabic ? 'ملخص سريع' : 'Quick summary' }}</h3>
                    <div class="chat-hero-list">
                        <div class="chat-hero-item">
                            <span>{{ $isArabic ? 'عدد المحادثات' : 'Conversations count' }}</span>
                            <strong>{{ $conversations->count() }}</strong>
                        </div>
                        <div class="chat-hero-item">
                            <span>{{ $isArabic ? 'المحادثة الحالية' : 'Current chat' }}</span>
                            <strong>{{ $selectedConversation ? '#' . $selectedConversation->id : '—' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="chat-layout">
            <div class="chat-card">
                <div class="chat-head">
                    <h2>{{ $isArabic ? 'ابدأ محادثة' : 'Start conversation' }}</h2>
                </div>

                <form method="POST" action="{{ route('chat.conversations.store') }}" class="chat-form">
                    @csrf

                    <select name="other_user_id" class="chat-select" required>
                        <option value="">{{ $isArabic ? 'اختر مستخدمًا' : 'Select a user' }}</option>
                        @foreach ($otherUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->phone ?? $user->email ?? '—' }}</option>
                        @endforeach
                    </select>

                    <input type="number" name="service_id" class="chat-input" placeholder="{{ $isArabic ? 'رقم الخدمة (اختياري)' : 'Service ID (optional)' }}">

                    <button type="submit" class="chat-btn">
                        {{ $isArabic ? 'إنشاء محادثة' : 'Create conversation' }}
                    </button>
                </form>

                <div class="chat-head" style="margin-top:10px;">
                    <h2>{{ $isArabic ? 'المحادثات' : 'Conversations' }}</h2>
                </div>

                @if ($conversations->count())
                    <div class="chat-list">
                        @foreach ($conversations as $conversation)
                            @php
                                $party = $otherParty($conversation);
                            @endphp

                            <a href="{{ route('chat.index', ['conversation' => $conversation->id]) }}"
                               class="chat-item {{ $selectedConversation && $selectedConversation->id === $conversation->id ? 'active' : '' }}">
                                <strong>{{ $party->name ?? ($isArabic ? 'مستخدم' : 'User') }}</strong>
                                <span>
                                    {{ $isArabic ? 'الخدمة:' : 'Service:' }}
                                    {{ $conversation->service->name_ar ?? $conversation->service->name_en ?? '—' }}
                                </span>
                                <span>
                                    {{ $conversation->lastMessage->body ?? ($isArabic ? 'لا توجد رسائل بعد' : 'No messages yet') }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="chat-empty">
                        {{ $isArabic ? 'لا توجد محادثات حتى الآن.' : 'No conversations yet.' }}
                    </div>
                @endif
            </div>

            <div class="chat-card">
                @if ($selectedConversation)
                    @php
                        $party = $otherParty($selectedConversation);
                    @endphp

                    <div class="chat-head">
                        <h2>{{ $party->name ?? ($isArabic ? 'مستخدم' : 'User') }}</h2>
                        <span>{{ $selectedConversation->service->name_ar ?? $selectedConversation->service->name_en ?? '—' }}</span>
                    </div>

                    @if ($selectedConversation->messages && $selectedConversation->messages->count())
                        <div class="message-list">
                            @foreach ($selectedConversation->messages->sortBy('created_at') as $message)
                                <div class="message-bubble {{ $message->sender_id === auth()->id() ? 'mine' : 'other' }}">
                                    <div>{{ $message->body }}</div>
                                    <div class="message-meta">
                                        {{ $message->sender->name ?? '—' }} • {{ optional($message->created_at)->format('Y-m-d H:i') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="chat-empty">
                            {{ $isArabic ? 'لا توجد رسائل بعد في هذه المحادثة.' : 'There are no messages in this conversation yet.' }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('chat.messages.store', $selectedConversation->id) }}" class="chat-form" style="margin-top:18px;">
                        @csrf
                        <textarea name="body" class="chat-textarea" placeholder="{{ $isArabic ? 'اكتب رسالتك هنا...' : 'Write your message here...' }}" required>{{ old('body') }}</textarea>
                        <button type="submit" class="chat-btn">
                            {{ $isArabic ? 'إرسال الرسالة' : 'Send message' }}
                        </button>
                    </form>
                @else
                    <div class="chat-empty">
                        {{ $isArabic ? 'اختر محادثة من القائمة أو أنشئ واحدة جديدة.' : 'Select a conversation from the list or create a new one.' }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection