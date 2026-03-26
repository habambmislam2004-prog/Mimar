<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $favorites = Favorite::query()
            ->with(['service.businessAccount', 'service.category', 'service.subcategory', 'service.images'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('public.favorites.index', compact('favorites'));
    }

    public function store(Request $request, Service $service): RedirectResponse
    {
        Favorite::query()->firstOrCreate([
            'user_id' => $request->user()->id,
            'service_id' => $service->id,
        ]);

        return back()->with('success', __('messages.created_successfully'));
    }

    public function destroy(Request $request, Service $service): RedirectResponse
    {
        Favorite::query()
            ->where('user_id', $request->user()->id)
            ->where('service_id', $service->id)
            ->delete();

        return back()->with('success', __('messages.deleted_successfully'));
    }
}