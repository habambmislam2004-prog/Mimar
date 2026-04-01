<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estimation\CalculateEstimationRequest;
use App\Services\Estimation\EstimationCalculationService;

class EstimationController extends Controller
{
    public function __construct(
        protected EstimationCalculationService $service
    ) {
    }

    public function calculate(CalculateEstimationRequest $request): JsonResponse
    {
        try {
            $estimation = $this->service->calculate(
                $request->user(),
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'data' => $estimation,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}