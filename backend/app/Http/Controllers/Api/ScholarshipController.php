<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Scholarship::query()
            ->with(['university:id,slug,name_th,name_en', 'country:id,slug,name_th,name_en'])
            ->where('is_active', true);

        if ($request->filled('country')) {
            $query->whereHas('country', fn ($q) => $q->where('slug', $request->string('country')));
        }

        $scholarships = $query
            ->orderByDesc('is_featured')
            ->paginate($request->integer('per_page', 12));

        return response()->json($scholarships);
    }

    public function show(string $slug): JsonResponse
    {
        $scholarship = Scholarship::query()
            ->with(['university', 'country', 'seo'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json(['data' => $scholarship]);
    }
}
