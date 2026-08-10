<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Post::query()
            ->with('category:id,slug,name_th,name_en')
            ->where('is_active', true)
            ->whereNotNull('published_at');

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
        }

        $posts = $query->latest('published_at')->paginate($request->integer('per_page', 12));

        return response()->json($posts);
    }

    public function show(string $slug): JsonResponse
    {
        $post = Post::query()
            ->with(['category', 'author:id,name', 'seo'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json(['data' => $post]);
    }
}
