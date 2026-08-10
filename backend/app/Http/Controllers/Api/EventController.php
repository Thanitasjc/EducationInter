<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $events = Event::query()
            ->where('is_active', true)
            ->when($request->boolean('upcoming'), fn ($q) => $q->where('starts_at', '>=', now()->subDay()))
            ->orderBy('starts_at')
            ->paginate($request->integer('per_page', 12));

        return response()->json($events);
    }

    public function show(string $slug): JsonResponse
    {
        $event = Event::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json(['data' => $event]);
    }
}
