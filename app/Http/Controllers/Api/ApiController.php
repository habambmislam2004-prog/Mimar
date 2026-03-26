<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    protected function successResponse(
        mixed $data = null,
        string $message = '',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message ?: __('messages.success'),
            'data' => $data,
        ], $status);
    }

    protected function errorResponse(
        string $message = '',
        mixed $errors = null,
        int $status = 400
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message ?: __('messages.server_error'),
            'errors' => $errors,
        ], $status);
    }
}