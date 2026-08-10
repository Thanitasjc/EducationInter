<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $programs = Program::query()
            ->active()
            ->forAgeGroup($request->query('age'))
            ->when($request->filled('language'), fn ($q) => $q->where('language', $request->string('language')))
            ->when($request->filled('country'), function ($q) use ($request) {
                $country = $request->string('country')->toString();
                $q->whereJsonContains('destinations', $country);
            })
            ->when($request->boolean('featured'), fn ($q) => $q->where('is_featured', true))
            ->orderBy('sort_order')
            ->orderBy('title_en')
            ->paginate($request->integer('per_page', 12));

        return response()->json($programs);
    }

    public function show(string $slug): JsonResponse
    {
        $program = Program::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json(['data' => $program]);
    }
}
