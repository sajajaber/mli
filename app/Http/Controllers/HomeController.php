<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Show;
use App\Models\SiteContent;

class HomeController extends Controller
{
    public function index()
    {
        $shows = Show::published()
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

        return view('home', compact('shows', 'mediaNews', 'mliNews', 'aboutPage'));
    }
}
