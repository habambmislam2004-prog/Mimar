<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Service\StoreServiceWebRequest;
use App\Http\Requests\Web\Service\UpdateServiceWebRequest;
use App\Models\BusinessAccount;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Service;
use App\Models\Subcategory;
use App\Services\Web\ServiceWebService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function __construct(
        protected ServiceWebService $serviceWebService
    ) {
    }

    public function index(Request $request): View
    {
        $services = Service::with(['businessAccount', 'category', 'subcategory', 'images'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $favoriteIds = [];

        if (Auth::check()) {
            $favoriteIds = Favorite::query()
                ->where('user_id', Auth::id())
                ->pluck('service_id')
                ->toArray();
        }

        return view('public.services.index', compact('services', 'favoriteIds'));
    }

    public function show(Service $service): View
    {
        $service->load(['businessAccount', 'category', 'subcategory', 'images']);

        $businessAccounts = BusinessAccount::query()
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->latest()
            ->get();

        $isFavorited = false;

        if (Auth::check()) {
            $isFavorited = Favorite::query()
                ->where('user_id', Auth::id())
                ->where('service_id', $service->id)
                ->exists();
        }

        return view('public.services.show', compact('service', 'businessAccounts', 'isFavorited'));
    }

    public function create(): View
    {
        $businessAccounts = BusinessAccount::latest()->get();
        $categories = Category::latest()->get();
        $subcategories = Subcategory::latest()->get();

        return view('public.services.create', compact(
            'businessAccounts',
            'categories',
            'subcategories'
        ));
    }

    public function store(StoreServiceWebRequest $request): RedirectResponse
    {
        $service = $this->serviceWebService->create(
            $request->validated(),
            $request->file('images', [])
        );

        return redirect()
            ->route('services.show', $service)
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(Service $service): View
    {
        $service->load(['images']);

        $businessAccounts = BusinessAccount::latest()->get();
        $categories = Category::latest()->get();
        $subcategories = Subcategory::latest()->get();

        return view('public.services.edit', compact(
            'service',
            'businessAccounts',
            'categories',
            'subcategories'
        ));
    }

    public function update(UpdateServiceWebRequest $request, Service $service): RedirectResponse
    {
        $service = $this->serviceWebService->update(
            $service,
            $request->validated(),
            $request->file('images', [])
        );

        return redirect()
            ->route('services.show', $service)
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Service $service): RedirectResponse
    {
        if (! Auth::check()) {
            abort(403);
        }

        // التحقق أن الخدمة تعود للمستخدم الحالي عبر حساب الأعمال
        $service->loadMissing(['businessAccount', 'images']);

        if (! $service->businessAccount || (int) $service->businessAccount->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // حذف الصور من التخزين
        foreach ($service->images as $image) {
            if (! empty($image->path) && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
        }

        // حذف العلاقات المرتبطة إن وجدت
        if (method_exists($service, 'images')) {
            $service->images()->delete();
        }

        if (method_exists($service, 'favorites')) {
            $service->favorites()->delete();
        }

        if (method_exists($service, 'reports')) {
            $service->reports()->delete();
        }

        if (method_exists($service, 'dynamicFieldValues')) {
            $service->dynamicFieldValues()->delete();
        }

        // إذا عندك طلبات مرتبطة بالخدمة وما بدك تمنعي الحذف
        if (method_exists($service, 'orders')) {
            $service->orders()->delete();
        }

        // إذا عندك تقييمات مرتبطة بالخدمة
        if (method_exists($service, 'ratings')) {
            $service->ratings()->delete();
        }

        $service->delete();

        return redirect()
            ->route('services.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}