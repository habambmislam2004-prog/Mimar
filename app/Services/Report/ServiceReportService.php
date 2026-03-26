<?php

namespace App\Services\Report;

use App\Models\User;
use App\Models\Service;
use App\Models\ServiceReport;
use Illuminate\Support\Collection;

class ServiceReportService
{
    public function create(User $user, Service $service, array $data): ServiceReport
    {
        return ServiceReport::query()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'reason' => $data['reason'],
            'status' => 'pending',
        ])->load(['user', 'service']);
    }

    public function listForAdmin(): Collection
    {
        return ServiceReport::query()
            ->with(['user', 'service'])
            ->latest()
            ->get();
    }

    public function resolve(User $admin, ServiceReport $report, string $status): ServiceReport
    {
        $report->update([
            'status' => $status,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        return $report->refresh()->load(['user', 'service']);
    }
}