<?php

namespace AppHttpControllers;

use AppModelsClient;
use AppModelsNews;
use AppModelsShow;
use AppModelsSiteContent;

class HomeController extends Controller
{
    public function index()
    {
        // Keep Eloquent results out of the application cache.
        // This avoids unserializing stale Eloquent Collection objects
        // after code/dependency changes.
        $shows = Show::published()
            ->with('category')
            ->whereNotNull('cover_image_path')
            ->latest('published_at')
            ->limit(12)
            ->get();

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
            'mediaNews' => $mediaNews,
            'mliNews' => $mliNews,
            'aboutPage' => $aboutPage,
            'aboutStats' => $aboutStats,
        ]);
    }
}
