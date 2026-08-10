<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Service::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $service = Service::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json(['data' => $service]);
    }
}
