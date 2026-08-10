<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Course;
use App\Models\Event;
use App\Models\Post;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\University;
use Illuminate\Http\JsonResponse;

class SitemapController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'countries' => Country::query()->where('is_active', true)->pluck('slug'),
            'universities' => University::query()->where('is_active', true)->pluck('slug'),
            'courses' => Course::query()->where('is_active', true)->pluck('slug'),
            'scholarships' => Scholarship::query()->where('is_active', true)->pluck('slug'),
            'programs' => Program::query()->where('is_active', true)->pluck('slug'),
            'posts' => Post::query()->where('is_active', true)->whereNotNull('published_at')->pluck('slug'),
            'events' => Event::query()->where('is_active', true)->pluck('slug'),
            'static' => [
                '',
                'about',
                'study-abroad',
                'learn-language',
                'learn-language/academic-year',
                'countries',
                'universities',
                'courses',
                'scholarships',
                'services',
                'blog',
                'events',
                'contact',
                'apply',
                'ielts',
            ],
        ]);
    }
}
