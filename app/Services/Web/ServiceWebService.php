<?php

namespace App\Services\Web;

use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ServiceWebService
{
    public function create(array $data, array $images = []): Service
    {
        return DB::transaction(function () use ($data, $images) {
            $payload = collect($data)->except(['images'])->toArray();

            $service = Service::create($payload);

            $this->storeImages($service, $images);

            return $service->load(['businessAccount', 'category', 'subcategory', 'images']);
        });
    }

    public function update(Service $service, array $data, array $images = []): Service
    {
        return DB::transaction(function () use ($service, $data, $images) {
            $payload = collect($data)->except(['images'])->toArray();

            $service->update($payload);

            $this->storeImages($service, $images);

            return $service->load(['businessAccount', 'category', 'subcategory', 'images']);
        });
    }

    protected function storeImages(Service $service, array $images = []): void
    {
        foreach ($images as $index => $image) {
            if (! $image instanceof UploadedFile) {
                continue;
            }

            $path = $image->store('services/images', 'public');

            ServiceImage::create([
                'service_id' => $service->id,
                'path' => $path,
                'is_primary' => $index === 0 && ! $service->images()->exists(),
                'sort_order' => $service->images()->count() + $index + 1,
            ]);
        }
    }
}