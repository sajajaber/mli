<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');

        if (!in_array($type, ['mli_news', 'media_news'], true)) {
            $type = null;
        }

        $featuredQuery = News::published();

        if ($type) {
            $featuredQuery->where('news_type', $type);
        }

        $featured = $featuredQuery
            ->latest('published_at')
            ->latest('created_at')
            ->first();

        $newsQuery = News::published();

        if ($type) {
            $newsQuery->where('news_type', $type);
        }

        if ($featured) {
            $newsQuery->where('id', '!=', $featured->id);
        }

        $news = $newsQuery
            ->latest('published_at')
            ->latest('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('news.index', compact('news', 'featured', 'type'));
    }

    public function show(string $slug)
    {
        $article = News::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = News::published()
            ->where('id', '!=', $article->id)
            ->where('news_type', $article->news_type)
            ->latest('published_at')
            ->latest('created_at')
            ->limit(3)
            ->get();

        return view('news.show', compact('article', 'related'));
    }
}
