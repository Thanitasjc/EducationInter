<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Course;
use App\Models\Event;
use App\Models\HomeSection;
use App\Models\PageContent;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Program;
use App\Models\Review;
use App\Models\Scholarship;
use App\Models\Service;
use App\Models\University;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $locale = $request->query('locale', 'th');

        return response()->json([
            'locale' => $locale,
            'hero' => PageContent::query()->where('key', 'hero')->value('value'),
            'sections' => HomeSection::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function (HomeSection $section) {
                    $data = $section->toArray();
                    $data['cover_url'] = $section->coverUrl();

                    return $data;
                })
                ->values(),
            'countries' => Country::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(8)
                ->get(),
            'universities' => University::query()
                ->with(['country:id,slug,name_th,name_en', 'city:id,slug,name_th,name_en'])
                ->where('is_active', true)
                ->where('is_featured', true)
                ->limit(8)
                ->get(),
            'courses' => Course::query()
                ->with(['university:id,slug,name_th,name_en,cover_path,logo_path'])
                ->where('is_active', true)
                ->where('is_popular', true)
                ->limit(8)
                ->get(),
            'programs' => Program::query()
                ->active()
                ->where('is_featured', true)
                ->orderBy('sort_order')
                ->limit(12)
                ->get(),
            'scholarships' => Scholarship::query()
                ->with([
                    'university:id,slug,name_th,name_en,logo_path,cover_path,about_th,about_en',
                    'country:id,slug,name_th,name_en',
                ])
                ->where('is_active', true)
                ->where('is_featured', true)
                ->limit(12)
                ->get(),
            'services' => Service::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'reviews' => Review::query()
                ->where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('sort_order')
                ->limit(8)
                ->get(),
            'partners' => Partner::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'posts' => Post::query()
                ->with('category:id,slug,name_th,name_en')
                ->where('is_active', true)
                ->whereNotNull('published_at')
                ->latest('published_at')
                ->limit(3)
                ->get(),
            'events' => Event::query()
                ->where('is_active', true)
                ->where('starts_at', '>=', now()->subDay())
                ->orderBy('starts_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
