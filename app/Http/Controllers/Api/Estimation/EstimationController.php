<?php

namespace App\Http\Controllers\Api\Estimation;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CityMaterialPrice;
use App\Models\Estimation;
use App\Models\EstimationServiceMatch;
use App\Models\EstimationType;
use App\Models\MaterialType;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstimationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $estimations = Estimation::query()
            ->with([
                'city:id,name_ar,name_en',
                'estimationType:id,code,name_ar,name_en',
                'items.materialType:id,code,name_ar,name_en,base_unit',
                'matches.service:id,name_ar,name_en,price,status',
            ])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Estimations fetched successfully.',
            'data' => $estimations,
        ]);
    }

    public function show(Request $request, Estimation $estimation): JsonResponse
    {
        if ((int) $estimation->user_id !== (int) $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $estimation->load([
            'city:id,name_ar,name_en',
            'estimationType:id,code,name_ar,name_en',
            'items.materialType:id,code,name_ar,name_en,base_unit',
            'matches.service:id,name_ar,name_en,price,status',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estimation details fetched successfully.',
            'data' => $estimation,
        ]);
    }

    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'city_id' => ['required', 'exists:cities,id'],
            'estimation_type_id' => ['required', 'exists:estimation_types,id'],
            'length' => ['nullable', 'numeric', 'min:0.1'],
            'width' => ['nullable', 'numeric', 'min:0.1'],
            'height' => ['nullable', 'numeric', 'min:0.1'],
            'area' => ['nullable', 'numeric', 'min:0.1'],
            'coats' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $type = EstimationType::query()->findOrFail($validated['estimation_type_id']);

        try {
            $result = DB::transaction(function () use ($validated, $type, $request) {
                $dimensions = $this->resolveDimensions($type->code, $validated);
                $items = $this->buildItems($type->code, $validated['city_id'], $dimensions, $validated);

                $subtotal = collect($items)->sum('line_total');
                $wasteCost = collect($items)->sum(function ($item) {
                    return $item['unit_price'] * $item['waste_quantity'];
                });
                $total = $subtotal;
                $duration = $this->estimateDurationDays($type->code, $dimensions['area']);

                $estimation = Estimation::query()->create([
                    'user_id' => $request->user()->id,
                    'city_id' => $validated['city_id'],
                    'estimation_type_id' => $type->id,
                    'input_payload' => [
                        'length' => $validated['length'] ?? null,
                        'width' => $validated['width'] ?? null,
                        'height' => $validated['height'] ?? null,
                        'area' => $validated['area'] ?? null,
                        'coats' => $validated['coats'] ?? 2,
                        'calculated_area' => $dimensions['area'],
                    ],
                    'subtotal_cost' => $subtotal,
                    'waste_cost' => $wasteCost,
                    'total_cost' => $total,
                    'estimated_duration_days' => $duration,
                    'notes' => 'Auto-generated estimation.',
                ]);

                foreach ($items as $item) {
                    $estimation->items()->create($item);
                }

                $this->matchSuggestedServices($estimation, $type->code);

                return $estimation->load([
                    'city:id,name_ar,name_en',
                    'estimationType:id,code,name_ar,name_en',
                    'items.materialType:id,code,name_ar,name_en,base_unit',
                    'matches.service:id,name_ar,name_en,price,status',
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Estimation calculated successfully.',
                'data' => $result,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate estimation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function resolveDimensions(string $typeCode, array $validated): array
    {
        $length = (float) ($validated['length'] ?? 0);
        $width = (float) ($validated['width'] ?? 0);
        $height = (float) ($validated['height'] ?? 0);
        $explicitArea = (float) ($validated['area'] ?? 0);

        $area = match ($typeCode) {
            'wall_building', 'painting', 'plastering' => $explicitArea > 0 ? $explicitArea : ($length * $height),
            'ceramic_installation' => $explicitArea > 0 ? $explicitArea : ($length * $width),
            default => throw new \RuntimeException('Unsupported estimation type.'),
        };

        if ($area <= 0) {
            throw new \RuntimeException('Area could not be calculated. Please provide valid dimensions.');
        }

        return [
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'area' => round($area, 3),
        ];
    }

    private function buildItems(string $typeCode, int $cityId, array $dimensions, array $validated): array
    {
        $area = $dimensions['area'];
        $coats = (int) ($validated['coats'] ?? 2);

        $formulaItems = match ($typeCode) {
            'wall_building' => [
                ['material_code' => 'block', 'qty' => $area * 12.5, 'waste' => 5],
                ['material_code' => 'cement', 'qty' => $area * 0.18, 'waste' => 7],
                ['material_code' => 'sand', 'qty' => $area * 0.05, 'waste' => 10],
            ],
            'painting' => [
                ['material_code' => 'paint', 'qty' => ($area / 8) * $coats, 'waste' => 8],
            ],
            'plastering' => [
                ['material_code' => 'plaster_mix', 'qty' => $area * 0.25, 'waste' => 10],
                ['material_code' => 'cement', 'qty' => $area * 0.08, 'waste' => 7],
            ],
            'ceramic_installation' => [
                ['material_code' => 'ceramic', 'qty' => $area, 'waste' => 10],
                ['material_code' => 'adhesive', 'qty' => $area / 4, 'waste' => 8],
            ],
            default => throw new \RuntimeException('No formula configured for this estimation type.'),
        };

        $items = [];

        foreach ($formulaItems as $formulaItem) {
            $material = MaterialType::query()
                ->where('code', $formulaItem['material_code'])
                ->firstOrFail();

            $cityPrice = CityMaterialPrice::query()
                ->where('city_id', $cityId)
                ->where('material_type_id', $material->id)
                ->where('is_active', true)
                ->first();

            $unitPrice = (float) ($cityPrice?->price ?? 0);
            $calculatedQty = round($formulaItem['qty'], 3);
            $wasteQty = round($calculatedQty * ($formulaItem['waste'] / 100), 3);
            $finalQty = round($calculatedQty + $wasteQty, 3);
            $lineTotal = round($finalQty * $unitPrice, 2);

            $items[] = [
                'material_type_id' => $material->id,
                'calculated_quantity' => $calculatedQty,
                'unit' => $material->base_unit,
                'unit_price' => $unitPrice,
                'waste_percentage' => $formulaItem['waste'],
                'waste_quantity' => $wasteQty,
                'final_quantity' => $finalQty,
                'line_total' => $lineTotal,
            ];
        }

        return $items;
    }

    private function estimateDurationDays(string $typeCode, float $area): int
    {
        return match ($typeCode) {
            'wall_building' => max(1, (int) ceil($area / 15)),
            'painting' => max(1, (int) ceil($area / 40)),
            'plastering' => max(1, (int) ceil($area / 20)),
            'ceramic_installation' => max(1, (int) ceil($area / 18)),
            default => 1,
        };
    }

    private function matchSuggestedServices(Estimation $estimation, string $typeCode): void
    {
        $categoryMap = [
            'wall_building' => 'Construction',
            'painting' => 'Painting',
            'plastering' => 'Plastering',
            'ceramic_installation' => 'Ceramics',
        ];

        $categoryName = $categoryMap[$typeCode] ?? null;

        if (! $categoryName) {
            return;
        }

        $category = Category::query()->where('name_en', $categoryName)->first();

        if (! $category) {
            return;
        }

        $services = Service::query()
            ->where('category_id', $category->id)
            ->where('status', 'approved')
            ->latest()
            ->take(5)
            ->get();

        foreach ($services as $service) {
            EstimationServiceMatch::query()->create([
                'estimation_id' => $estimation->id,
                'service_id' => $service->id,
                'match_reason' => 'Matched by estimation type category.',
            ]);
        }
    }
}