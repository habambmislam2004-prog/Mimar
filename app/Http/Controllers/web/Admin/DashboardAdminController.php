<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessAccount;
use App\Models\Order;
use App\Models\Service;
use Illuminate\View\View;

class DashboardAdminController extends Controller
{
    public function index(): View
    {
        $stats = [
            [
                'label_ar' => 'الخدمات',
                'label_en' => 'Services',
                'value' => Service::count(),
                'note_ar' => 'إجمالي الخدمات داخل المنصة',
                'note_en' => 'Total services on the platform',
                'icon' => '🛠️',
            ],
            [
                'label_ar' => 'حسابات الأعمال',
                'label_en' => 'Business Accounts',
                'value' => BusinessAccount::count(),
                'note_ar' => 'مزودو الخدمة والحسابات التجارية',
                'note_en' => 'Providers and business accounts',
                'icon' => '🏢',
            ],
            [
                'label_ar' => 'الطلبات',
                'label_en' => 'Orders',
                'value' => Order::count(),
                'note_ar' => 'طلبات نشطة وقابلة للمتابعة',
                'note_en' => 'Active and trackable orders',
                'icon' => '📦',
            ],
            [
                'label_ar' => 'التقديرات',
                'label_en' => 'Estimations',
                'value' => class_exists(\App\Models\Estimation::class) ? \App\Models\Estimation::count() : 0,
                'note_ar' => 'نتائج تقدير محفوظة',
                'note_en' => 'Saved estimation results',
                'icon' => '📐',
            ],
        ];

        $quickActions = [
            [
                'title_ar' => 'إدارة الخدمات',
                'title_en' => 'Manage Services',
                'text_ar' => 'استعراض الخدمات وإدارتها ومتابعة حالتها.',
                'text_en' => 'Browse, review, and manage listed services.',
                'link' => route('admin.services.index'),
                'button_ar' => 'فتح الخدمات',
                'button_en' => 'Open Services',
            ],
            [
                'title_ar' => 'حسابات الأعمال',
                'title_en' => 'Business Accounts',
                'text_ar' => 'متابعة مقدمي الخدمات والبيانات التجارية.',
                'text_en' => 'Track providers and business profile information.',
                'link' => route('admin.business-accounts.index'),
                'button_ar' => 'عرض الحسابات',
                'button_en' => 'View Accounts',
            ],
            [
                'title_ar' => 'إدارة الطلبات',
                'title_en' => 'Manage Orders',
                'text_ar' => 'عرض جميع الطلبات ومتابعة حالاتها وقبولها أو رفضها أو حذفها.',
                'text_en' => 'View all orders and manage their statuses, acceptance, rejection, or deletion.',
                'link' => route('admin.orders.index'),
                'button_ar' => 'فتح الطلبات',
                'button_en' => 'Open Orders',
            ],
            [
                'title_ar' => 'إدارة المستخدمين',
                'title_en' => 'Manage Users',
                'text_ar' => 'إضافة وتعديل وحذف المستخدمين والمدراء.',
                'text_en' => 'Create, update, and manage users and admins.',
                'link' => route('admin.users.index'),
                'button_ar' => 'فتح المستخدمين',
                'button_en' => 'Open Users',
            ],
            [
                'title_ar' => 'إدارة الأدوار',
                'title_en' => 'Manage Roles',
                'text_ar' => 'عرض الأدوار، تعديلها، حذفها، وتحديد الصلاحيات الخاصة بها.',
                'text_en' => 'View, edit, delete roles, and assign permissions.',
                'link' => route('admin.roles.index'),
                'button_ar' => 'فتح الأدوار',
                'button_en' => 'Open Roles',
            ],
        ];

        $recentActivities = collect();

        $latestService = Service::latest()->first();
        if ($latestService) {
            $recentActivities->push([
                'title_ar' => 'تمت إضافة خدمة جديدة إلى المنصة',
                'title_en' => 'A new service has been added to the platform',
                'time' => $latestService->created_at,
            ]);
        }

        $latestBusinessAccount = BusinessAccount::latest()->first();
        if ($latestBusinessAccount) {
            $recentActivities->push([
                'title_ar' => 'تم تسجيل حساب أعمال جديد',
                'title_en' => 'A new business account has been registered',
                'time' => $latestBusinessAccount->created_at,
            ]);
        }

        $latestOrder = Order::latest()->first();
        if ($latestOrder) {
            $recentActivities->push([
                'title_ar' => 'تم إنشاء أو تحديث طلب جديد',
                'title_en' => 'A new order has been created or updated',
                'time' => $latestOrder->created_at,
            ]);
        }

        if (class_exists(\App\Models\Estimation::class)) {
            $latestEstimation = \App\Models\Estimation::latest()->first();

            if ($latestEstimation) {
                $recentActivities->push([
                    'title_ar' => 'تم إنشاء تقدير جديد',
                    'title_en' => 'A new estimation has been created',
                    'time' => $latestEstimation->created_at,
                ]);
            }
        }

        $recentActivities = $recentActivities
            ->sortByDesc('time')
            ->take(6)
            ->values();

        return view('admin.dashboard', compact(
            'stats',
            'quickActions',
            'recentActivities'
        ));
    }
}