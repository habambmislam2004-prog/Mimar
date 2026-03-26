<?php

namespace App\Observers;

use App\Models\Rating;
use App\Models\Service;

class RatingObserver
{
    public function created(Rating $rating): void
    {
        $service = Service::query()->find($rating->service_id);

        if (! $service) {
            return;
        }

        $average = $service->ratings()->avg('score');
        $count = $service->ratings()->count();

        $service->update([
            'average_rating' => round((float) $average, 2),
            'ratings_count' => $count,
        ]);
    }
}