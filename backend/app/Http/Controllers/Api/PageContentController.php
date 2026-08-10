<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\JsonResponse;

class PageContentController extends Controller
{
    public function show(string $key): JsonResponse
    {
        $page = PageContent::query()->where('key', $key)->firstOrFail();

        return response()->json(['data' => $page]);
    }
}
