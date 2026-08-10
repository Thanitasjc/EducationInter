<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    public function index(): JsonResponse
    {
        $countries = Country::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => $countries]);
    }

    public function show(string $slug): JsonResponse
    {
        $country = Country::query()
            ->with([
                'universities' => fn ($q) => $q->where('is_active', true)->limit(12),
                'scholarships' => fn ($q) => $q->where('is_active', true)->limit(6),
                'seo',
            ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json(['data' => $country]);
    }
}
