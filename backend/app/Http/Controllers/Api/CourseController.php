<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::query()
            ->with(['university:id,slug,name_th,name_en,country_id,cover_path,logo_path', 'category:id,slug,name_th,name_en'])
            ->where('is_active', true);

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($builder) use ($q) {
                $builder->where('name_th', 'like', "%{$q}%")
                    ->orWhere('name_en', 'like', "%{$q}%");
            });
        }

        if ($request->filled('degree_level')) {
            $query->where('degree_level', $request->string('degree_level'));
        }

        if ($request->filled('university')) {
            $query->whereHas('university', fn ($q) => $q->where('slug', $request->string('university')));
        }

        $courses = $query
            ->orderByDesc('is_popular')
            ->paginate($request->integer('per_page', 12));

        return response()->json($courses);
    }

    public function show(string $slug): JsonResponse
    {
        $course = Course::query()
            ->with(['university.country', 'category', 'seo'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json(['data' => $course]);
    }
}
