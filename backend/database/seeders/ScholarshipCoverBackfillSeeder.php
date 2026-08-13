<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use Illuminate\Database\Seeder;

/**
 * Safe insert/update of scholarship cover URLs only (no wipe).
 */
class ScholarshipCoverBackfillSeeder extends Seeder
{
    public function run(): void
    {
        $covers = [
            'manchester-undergraduate-scholarship' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80',
            'manchester-masters-scholarship' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80',
            'ucl-global-undergraduate' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1200&q=80',
            'ucl-global-masters' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1200&q=80',
            'melbourne-international-undergraduate' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1200&q=80',
            'melbourne-international-masters' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1200&q=80',
            'toronto-undergraduate-scholarship' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80',
            'toronto-masters-scholarship' => 'https://images.unsplash.com/photo-1517935706615-2717063c0395?auto=format&fit=crop&w=1200&q=80',
        ];

        foreach ($covers as $slug => $url) {
            Scholarship::query()
                ->where('slug', $slug)
                ->where(function ($q) {
                    $q->whereNull('cover_path')->orWhere('cover_path', '');
                })
                ->update(['cover_path' => $url]);
        }
    }
}
