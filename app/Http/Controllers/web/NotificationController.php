<?php

namespace App\Http\Controllers\Web;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\BusinessAccount;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $notifications = collect();

        $orders = Order::query()
            ->with(['service', 'receiverBusinessAccount', 'user'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        foreach ($orders as $order) {
            $serviceName = $order->service?->name_ar
                ?? $order->service?->name_en
                ?? '—';

            $notifications->push([
                'type' => 'order',
                'title_ar' => 'تحديث على طلب خدمة',
                'title_en' => 'Service order update',
                'body_ar' => match ($order->status) {
                    'accepted' => "تم قبول طلبك للخدمة: {$serviceName}",
                    'rejected' => "تم رفض طلبك للخدمة: {$serviceName}",
                    'cancelled' => "تم إلغاء طلبك للخدمة: {$serviceName}",
                    default => "طلبك للخدمة: {$serviceName} ما زال قيد المراجعة",
                },
                'body_en' => match ($order->status) {
                    'accepted' => "Your request for service '{$serviceName}' has been accepted.",
                    'rejected' => "Your request for service '{$serviceName}' has been rejected.",
                    'cancelled' => "Your request for service '{$serviceName}' has been cancelled.",
                    default => "Your request for service '{$serviceName}' is still under review.",
                },
                'status' => $order->status,
                'date' => $order->updated_at ?? $order->created_at,
            ]);
        }

        $businessAccounts = BusinessAccount::query()
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        foreach ($businessAccounts as $businessAccount) {
            $businessName = $businessAccount->name_ar
                ?? $businessAccount->name_en
                ?? '—';

            $notifications->push([
                'type' => 'business_account',
                'title_ar' => 'تحديث على حساب الأعمال',
                'title_en' => 'Business account update',
                'body_ar' => match ($businessAccount->status) {
                    'approved' => "تمت الموافقة على حساب الأعمال: {$businessName}",
                    'rejected' => "تم رفض حساب الأعمال: {$businessName}",
                    default => "حساب الأعمال: {$businessName} ما زال قيد المراجعة",
                },
                'body_en' => match ($businessAccount->status) {
                    'approved' => "Your business account '{$businessName}' has been approved.",
                    'rejected' => "Your business account '{$businessName}' has been rejected.",
                    default => "Your business account '{$businessName}' is still under review.",
                },
                'status' => $businessAccount->status,
                'date' => $businessAccount->updated_at ?? $businessAccount->created_at,
            ]);
        }

        $notifications = $notifications
            ->sortByDesc('date')
            ->values();

        $stats = [
            'total' => $notifications->count(),
            'orders' => $notifications->where('type', 'order')->count(),
            'business_accounts' => $notifications->where('type', 'business_account')->count(),
            'latest' => $notifications->first(),
        ];

        return view('public.notifications.index', compact('notifications', 'stats'));
    }
}