<?php

namespace App\Services\Estimation;

use App\Models\User;
use App\Models\Estimation;
use App\Models\MaterialType;
use App\Models\EstimationType;
use App\Models\EstimationItem;
use App\Exceptions\DomainException;
use Illuminate\Support\Facades\DB;

class EstimationCalculationService
{
    public function __construct(
        protected MaterialPriceResolverService $priceResolver,
        protected EstimationMatcherService $matcherService
    ) {
    }

    public function calculate(?User $user, array $data): Estimation
    {
        $estimationType = EstimationType::query()->findOrFail($data['estimation_type_id']);

        return DB::transaction(function () use ($user, $data, $estimationType) {
            $inputPayload = [
                'length' => $data['length'] ?? null,
                'width' => $data['width'] ?? null,
                'height' => $data['height'] ?? null,
                'thickness' => $data['thickness'] ?? null,
                'area' => $data['area'] ?? null,
            ];

            $estimation = Estimation::query()->create([
                'user_id' => $user?->id,
                'city_id' => $data['city_id'],
                'estimation_type_id' => $estimationType->id,
                'input_payload' => $inputPayload,
                'subtotal_cost' => 0,
                'waste_cost' => 0,
                'total_cost' => 0,
                'estimated_duration_days' => null,
                'notes' => $data['notes'] ?? null,
            ]);

            [$itemsData, $durationDays] = $this->calculateItems(
                $estimationType->code,
                $data['city_id'],
                $inputPayload
            );

            $subtotal = 0;
            $wasteCost = 0;

            foreach ($itemsData as $item) {
                $createdItem = EstimationItem::query()->create([
                    'estimation_id' => $estimation->id,
                    'material_type_id' => $item['material_type_id'],
                    'calculated_quantity' => $item['calculated_quantity'],
                    'unit' => $item['unit'],
                    'unit_price' => $item['unit_price'],
                    'waste_percentage' => $item['waste_percentage'],
                    'waste_quantity' => $item['waste_quantity'],
                    'final_quantity' => $item['final_quantity'],
                    'line_total' => $item['line_total'],
                ]);

                $subtotal += $createdItem->calculated_quantity * $createdItem->unit_price;
                $wasteCost += $createdItem->waste_quantity * $createdItem->unit_price;
            }

            $estimation->update([
                'subtotal_cost' => $subtotal,
                'waste_cost' => $wasteCost,
                'total_cost' => $subtotal + $wasteCost,
                'estimated_duration_days' => $durationDays,
            ]);

            $this->matcherService->match($estimation);

            return $estimation->refresh()->load([
                'city',
                'estimationType',
                'items.materialType',
                'matches.service',
                'matches.businessAccount',
            ]);
        });
    }

    protected function calculateItems(string $typeCode, int $cityId, array $inputPayload): array
    {
        return match ($typeCode) {
            'wall_building' => $this->calculateWallBuilding($cityId, $inputPayload),
            'painting' => $this->calculatePainting($cityId, $inputPayload),
            'plastering' => $this->calculatePlastering($cityId, $inputPayload),
            'ceramic_installation' => $this->calculateCeramicInstallation($cityId, $inputPayload),
            default => throw new DomainException(__('messages.estimation_type_not_supported')),
        };
    }

    protected function calculateWallBuilding(int $cityId, array $input): array
    {
        $length = $input['length'] ?? null;
        $height = $input['height'] ?? null;

        if (! $length || ! $height) {
            throw new DomainException(__('messages.wall_building_requires_length_and_height'));
        }

        $area = $length * $height;

        $items = [
            $this->buildItem($cityId, 'block', $area * 12.5, 10),
            $this->buildItem($cityId, 'cement', $area * 0.18, 10),
            $this->buildItem($cityId, 'sand', $area * 0.05, 10),
        ];

        $durationDays = max(1, (int) ceil($area / 15));

        return [$items, $durationDays];
    }

    protected function calculatePainting(int $cityId, array $input): array
    {
        $area = $this->resolveArea($input);

        $items = [
            $this->buildItem($cityId, 'paint', $area / 10, 8),
        ];

        $durationDays = max(1, (int) ceil($area / 50));

        return [$items, $durationDays];
    }

    protected function calculatePlastering(int $cityId, array $input): array
    {
        $area = $this->resolveArea($input);

        $items = [
            $this->buildItem($cityId, 'plaster_mix', $area * 0.20, 10),
        ];

        $durationDays = max(1, (int) ceil($area / 20));

        return [$items, $durationDays];
    }

    protected function calculateCeramicInstallation(int $cityId, array $input): array
    {
        $area = $this->resolveArea($input);

        $items = [
            $this->buildItem($cityId, 'ceramic', $area, 7),
            $this->buildItem($cityId, 'adhesive', $area * 0.22, 7),
        ];

        $durationDays = max(1, (int) ceil($area / 25));

        return [$items, $durationDays];
    }

    protected function resolveArea(array $input): float
    {
        if (! empty($input['area'])) {
            return (float) $input['area'];
        }

        if (! empty($input['length']) && ! empty($input['width'])) {
            return (float) $input['length'] * (float) $input['width'];
        }

        if (! empty($input['length']) && ! empty($input['height'])) {
            return (float) $input['length'] * (float) $input['height'];
        }

        throw new DomainException(__('messages.estimation_area_cannot_be_resolved'));
    }

    protected function buildItem(
        int $cityId,
        string $materialCode,
        float $calculatedQuantity,
        float $wastePercentage
    ): array {
        $material = MaterialType::query()
            ->where('code', $materialCode)
            ->firstOrFail();

        $unitPrice = $this->priceResolver->getPrice($cityId, $material->id);
        $wasteQuantity = $calculatedQuantity * ($wastePercentage / 100);
        $finalQuantity = $calculatedQuantity + $wasteQuantity;
        $lineTotal = $finalQuantity * $unitPrice;

        return [
            'material_type_id' => $material->id,
            'calculated_quantity' => round($calculatedQuantity, 3),
            'unit' => $material->base_unit,
            'unit_price' => round($unitPrice, 2),
            'waste_percentage' => round($wastePercentage, 2),
            'waste_quantity' => round($wasteQuantity, 3),
            'final_quantity' => round($finalQuantity, 3),
            'line_total' => round($lineTotal, 2),
        ];
    }
}