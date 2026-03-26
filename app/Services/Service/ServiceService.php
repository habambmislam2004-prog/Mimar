<?php

namespace App\Services\Service;

use App\Models\Service;
use App\Models\BusinessAccount;
use App\Models\User;
use App\Exceptions\DomainException;
use Illuminate\Support\Facades\DB;
use App\Notifications\ServiceStatusChangedNotification;

class ServiceService
{
    public function listForUser(BusinessAccount $businessAccount)
    {
        return Service::query()
            ->where('business_account_id', $businessAccount->id)
            ->latest()
            ->get();
    }

    public function create(BusinessAccount $businessAccount, array $data): Service
    {
        if (!$businessAccount->isApproved()) {
            throw new DomainException(__('messages.business_account_not_approved'));
        }

        return DB::transaction(function () use ($businessAccount, $data) {
            $service = Service::query()->create([
                'business_account_id' => $businessAccount->id,
                'category_id' => $data['category_id'],
                'subcategory_id' => $data['subcategory_id'],
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'status' => 'pending',
            ]);

            return $service->refresh();
        });
    }

    public function update(Service $service, array $data): Service
    {
        $service->update($data);
        return $service->refresh();
    }

    public function delete(Service $service): void
    {
        $service->delete();
    }

    public function approve(User $admin, Service $service): Service
    {
        $service->update([
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        return $service->refresh();
        $service->businessAccount?->user?->notify(
    new ServiceStatusChangedNotification($service));
    }

    public function reject(User $admin, Service $service, string $reason): Service
    {
        $service->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_by' => $admin->id,
            'rejected_at' => now(),
        ]);

        return $service->refresh();
        $service->businessAccount?->user?->notify(
    new ServiceStatusChangedNotification($service));
    }
}