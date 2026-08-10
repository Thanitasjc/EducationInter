<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use Illuminate\Http\JsonResponse;

class PostCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = PostCategory::query()
            ->whereHas('posts', fn ($q) => $q->where('is_active', true)->whereNotNull('published_at'))
            ->orderBy('name_en')
            ->get(['id', 'slug', 'name_th', 'name_en']);

        return response()->json(['data' => $categories]);
    }
}
