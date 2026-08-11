<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\HomeSection;
use App\Models\University;
use Illuminate\Console\Command;

class RestoreDemoMediaUrls extends Command
{
    protected $signature = 'media:restore-demo-urls';

    protected $description = 'Replace missing relative storage covers with durable Unsplash URLs (Render ephemeral disk recovery)';

    public function handle(): int
    {
        $countries = [
            'uk' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=1200&q=80',
            'australia' => 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?auto=format&fit=crop&w=1200&q=80',
            'usa' => 'https://images.unsplash.com/photo-1485738422979-f5c462d49f74?auto=format&fit=crop&w=1200&q=80',
            'canada' => 'https://images.unsplash.com/photo-1517935706615-2717063c0395?auto=format&fit=crop&w=1200&q=80',
            'new-zealand' => 'https://images.unsplash.com/photo-1469521669194-babb45599dbd?auto=format&fit=crop&w=1200&q=80',
            'ireland' => 'https://images.unsplash.com/photo-1590080875515-8a3a8dc5735e?auto=format&fit=crop&w=1200&q=80',
            'singapore' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=1200&q=80',
        ];

        $universities = [
            'university-of-manchester' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80',
            'university-college-london' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1200&q=80',
            'university-of-melbourne' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1200&q=80',
            'university-of-toronto' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80',
        ];

        $fixed = 0;

        foreach ($countries as $slug => $url) {
            $country = Country::query()->where('slug', $slug)->first();
            if ($country && $this->isRelativePath($country->cover_path)) {
                $country->update(['cover_path' => $url]);
                $this->line("Country {$slug} restored");
                $fixed++;
            }
        }

        foreach ($universities as $slug => $url) {
            $university = University::query()->where('slug', $slug)->first();
            if ($university && $this->isRelativePath($university->cover_path)) {
                $university->update(['cover_path' => $url]);
                $this->line("University {$slug} restored");
                $fixed++;
            }
        }

        $section = HomeSection::query()->where('key', 'bachelor-pathways')->first();
        if ($section && $this->isRelativePath($section->cover_path)) {
            $section->update([
                'cover_path' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=80',
            ]);
            $this->line('HomeSection bachelor-pathways restored');
            $fixed++;
        }

        // Any other relative cover_path on countries/universities without mapping → null (frontend fallback)
        foreach ([Country::class, University::class] as $model) {
            $model::query()
                ->whereNotNull('cover_path')
                ->get()
                ->each(function ($row) use (&$fixed) {
                    if ($this->isRelativePath($row->cover_path)) {
                        $row->update(['cover_path' => null]);
                        $this->warn("Cleared broken relative cover on {$row->slug}");
                        $fixed++;
                    }
                });
        }

        $this->info("Done. Fixed {$fixed} record(s).");

        return self::SUCCESS;
    }

    private function isRelativePath(?string $path): bool
    {
        if (! is_string($path) || $path === '') {
            return false;
        }

        return ! str_starts_with($path, 'http://') && ! str_starts_with($path, 'https://');
    }
}
