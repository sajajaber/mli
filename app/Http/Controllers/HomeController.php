<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\News;
use App\Models\Show;
use App\Models\SiteContent;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $homeData = Cache::remember(
            'public.home.data',
            now()->addSeconds(30),
            function (): array {
                return [
                    'shows' => Show::published()
                        ->with('category')
                        ->whereNotNull('cover_image_path')
                        ->latest('published_at')
                        ->limit(12)
                        ->get(),

                    'mediaNews' => News::published()
                        ->mediaNews()
                        ->latest('published_at')
                        ->limit(6)
                        ->get(),

                    'mliNews' => News::published()
                        ->mliNews()
                        ->latest('published_at')
                        ->limit(6)
                        ->get(),

                    'aboutPage' => SiteContent::published()
                        ->forKey('about_us')
                        ->first(),
                ];
            }
        );

        $aboutStats = Cache::remember(
            'public.home.about_stats',
            now()->addSeconds(30),
            fn (): array => [
                'shows' => Show::published()->count(),
                'clients' => Client::count(),
            ]
        );

        return view('home', [
            ...$homeData,
            'aboutStats' => $aboutStats,
        ]);
    }
}
