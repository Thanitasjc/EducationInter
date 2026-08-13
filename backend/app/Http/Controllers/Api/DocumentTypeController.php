<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\JsonResponse;

class DocumentTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => DocumentType::query()
                ->orderByDesc('is_required')
                ->orderBy('name_en')
                ->get(['id', 'slug', 'name_th', 'name_en', 'is_required']),
        ]);
    }
}
