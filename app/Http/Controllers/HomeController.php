<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\News;
use App\Models\HeroAdvertisement;
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

        $heroAdvertisements = HeroAdvertisement::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // Keep the public hero populated until the admin adds dedicated hero artwork.
        if ($heroAdvertisements->isEmpty()) {
            $heroAdvertisements = $shows
                ->filter(fn ($show) => filled($show->cover_image_path))
                ->take(5)
                ->map(fn ($show) => (object) [
                    'image_url' => $show->cover_image_url,
                    'image_alt' => $show->cover_image_alt
                        ?: (app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en),
                ])
                ->values();
        }

        $categories = Category::query()
            ->withCount([
                'shows as published_shows_count' => fn ($query) => $query->published(),
            ])
            ->orderBy('name_en')
            ->get()
            ->filter(fn ($category) => $category->published_shows_count > 0)
            ->values();

        $people = Person::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $clients = Client::query()
            ->orderBy('sort_order')
            ->orderBy('name')
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
            'heroAdvertisements' => $heroAdvertisements,
            'categories' => $categories,
            'people' => $people,
            'clients' => $clients,
            'mediaServices' => $mediaServices,
            'mediaNews' => $mediaNews,
            'mliNews' => $mliNews,
            'aboutPage' => $aboutPage,
            'aboutStats' => $aboutStats,
        ]);
    }
}
