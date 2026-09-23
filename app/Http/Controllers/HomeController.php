<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\News;
use App\Models\Person;
use App\Models\Show;
use App\Models\SiteContent;

class HomeController extends Controller
{
    public function index()
    {
        // Keep Eloquent results out of the application cache.
        // This avoids unserializing stale Eloquent Collection objects
        // after code/dependency changes.
        $shows = Show::published()
            ->with('category')
            ->latest('published_at')
            ->limit(12)
            ->get();

        $newReleases = Show::published()
            ->where('is_new_release', true)
            ->whereNotNull('cover_image_path')
            ->with('category')
            ->orderBy('sort_order')
            ->latest('published_at')
            ->limit(8)
            ->get();

        $people = Person::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $mediaServices = SiteContent::published()
            ->whereIn('key', [
                'media_service_1',
                'media_service_2',
                'media_service_3',
                'media_service_4',
            ])
            ->get()
            ->sortBy(fn ($item) => intval((string) str($item->key)->afterLast('_')))
            ->values();

        $mediaNews = News::published()
            ->mediaNews()
            ->latest('published_at')
            ->limit(6)
            ->get();

        $mliNews = News::published()
            ->mliNews()
            ->latest('published_at')
            ->limit(6)
            ->get();

        $aboutPage = SiteContent::published()
            ->forKey('about_us')
            ->first();

        $aboutStats = [
            'shows' => Show::published()->count(),
            'clients' => Client::count(),
        ];

        return view('home', [
            'shows' => $shows,
            'newReleases' => $newReleases,
            'people' => $people,
            'mediaServices' => $mediaServices,
            'mediaNews' => $mediaNews,
            'mliNews' => $mliNews,
            'aboutPage' => $aboutPage,
            'aboutStats' => $aboutStats,
        ]);
    }
}
