<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = University::query()
            ->with(['country:id,slug,name_th,name_en', 'city:id,slug,name_th,name_en'])
            ->where('is_active', true);

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($builder) use ($q) {
                $builder->where('name_th', 'like', "%{$q}%")
                    ->orWhere('name_en', 'like', "%{$q}%");
            });
        }

        if ($request->filled('country')) {
            $query->whereHas('country', fn ($q) => $q->where('slug', $request->string('country')));
        }

        if ($request->filled('city')) {
            $query->whereHas('city', fn ($q) => $q->where('slug', $request->string('city')));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('tuition_max')) {
            $query->where('tuition_min', '<=', $request->float('tuition_max'));
        }

        $universities = $query
            ->orderByDesc('is_featured')
            ->orderBy('ranking_qs')
            ->paginate($request->integer('per_page', 12));

        return response()->json($universities);
    }

    public function show(string $slug): JsonResponse
    {
        $university = University::query()
            ->with([
                'country',
                'city',
                'courses' => fn ($q) => $q->where('is_active', true),
                'scholarships' => fn ($q) => $q->where('is_active', true),
                'seo',
            ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json(['data' => $university]);
    }
}
