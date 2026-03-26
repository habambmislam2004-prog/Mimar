<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
       // dd('HOME CONTROLLER REACHED');

        $featuredServices = Service::with(['businessAccount', 'category', 'subcategory', 'images'])
            ->latest()
            ->take(3)
            ->get();

        return view('public.home', compact('featuredServices'));
    }
}