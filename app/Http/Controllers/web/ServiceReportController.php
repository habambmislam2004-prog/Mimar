<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServiceReportController extends Controller
{
    public function store(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        ServiceReport::query()->create([
            'user_id' => $request->user()->id,
            'service_id' => $service->id,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return back()->with('success', __('messages.created_successfully'));
    }
}