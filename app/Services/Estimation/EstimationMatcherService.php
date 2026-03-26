<?php

namespace App\Services\Estimation;

use App\Models\Service;
use App\Models\Estimation;
use App\Models\EstimationServiceMatch;

class EstimationMatcherService
{
    public function match(Estimation $estimation): void
    {
        $code = $estimation->estimationType?->code;

        $categoryKeywords = match ($code) {
            'wall_building' => ['بناء', 'Construction'],
            'painting' => ['دهان', 'Painting'],
            'plastering' => ['تلبيس', 'Plastering'],
            'ceramic_installation' => ['سيراميك', 'Ceramics'],
            default => [],
        };

        if (empty($categoryKeywords)) {
            return;
        }

        $services = Service::query()
            ->with('businessAccount')
            ->where('status', 'approved')
            ->where(function ($query) use ($categoryKeywords) {
                foreach ($categoryKeywords as $keyword) {
                    $query->orWhere('name_ar', 'like', "%{$keyword}%")
                        ->orWhere('name_en', 'like', "%{$keyword}%");
                }
            })
            ->limit(5)
            ->get();

        foreach ($services as $index => $service) {
            EstimationServiceMatch::query()->create([
                'estimation_id' => $estimation->id,
                'service_id' => $service->id,
                'business_account_id' => $service->business_account_id,
                'match_type' => 'service',
                'score' => max(0, 100 - ($index * 10)),
            ]);
        }
    }
}