<?php

namespace App\Services\Web;

use App\Enums\BusinessAccountStatus;
use App\Models\BusinessAccount;
use App\Models\BusinessAccountDocument;
use App\Models\BusinessAccountImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class BusinessAccountWebService
{
    public function create(User $user, array $data, array $images = [], array $documents = []): BusinessAccount
    {
        return DB::transaction(function () use ($user, $data, $images, $documents) {
            $payload = collect($data)->except(['images', 'documents'])->toArray();
            $payload['user_id'] = $user->id;
            $payload['status'] = BusinessAccountStatus::PENDING->value;

            $businessAccount = BusinessAccount::create($payload);

            $this->storeImages($businessAccount, $images);
            $this->storeDocuments($businessAccount, $documents);

            return $businessAccount->load(['city', 'activityType', 'images', 'documents']);
        });
    }

    public function update(BusinessAccount $businessAccount, array $data, array $images = [], array $documents = []): BusinessAccount
    {
        return DB::transaction(function () use ($businessAccount, $data, $images, $documents) {
            $payload = collect($data)->except(['images', 'documents'])->toArray();

            $payload['status'] = BusinessAccountStatus::PENDING->value;
            $payload['rejection_reason'] = null;
            $payload['rejected_by'] = null;
            $payload['rejected_at'] = null;

            $businessAccount->update($payload);

            $this->storeImages($businessAccount, $images);
            $this->storeDocuments($businessAccount, $documents);

            return $businessAccount->load(['city', 'activityType', 'images', 'documents']);
        });
    }

    protected function storeImages(BusinessAccount $businessAccount, array $images = []): void
{
    foreach ($images as $index => $image) {
        if (!$image instanceof UploadedFile) {
            continue;
        }

        $path = $image->store('business-accounts/images', 'public');

        BusinessAccountImage::create([
            'business_account_id' => $businessAccount->id,
            'path' => $path,
            'is_primary' => $index === 0,
            'sort_order' => $index + 1,
        ]);
    }
}

protected function storeDocuments(BusinessAccount $businessAccount, array $documents = []): void
{
    foreach ($documents as $document) {
        if (!$document instanceof UploadedFile) {
            continue;
        }

        $path = $document->store('business-accounts/documents', 'public');

        BusinessAccountDocument::create([
            'business_account_id' => $businessAccount->id,
            'file_name' => $document->getClientOriginalName(),
            'file_path' => $path,
            'document_type' => $document->getClientOriginalExtension(),
        ]);
    }
}
}