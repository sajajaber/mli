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

        $published = fn ($query) => $query
            ->published()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        $featuredQuery = News::query();
        $published($featuredQuery);

        if ($type) {
            $featuredQuery->where('news_type', $type);
        }

        $featured = $featuredQuery
            ->latest('published_at')
            ->first();

        $newsQuery = News::query();
        $published($newsQuery);

        if ($type) {
            $newsQuery->where('news_type', $type);
        }

        if ($featured) {
            $newsQuery->whereKeyNot($featured->id);
        }

        $news = $newsQuery
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('news.index', compact('news', 'featured', 'type'));
    }

    public function show(string $slug)
    {
        $article = News::published()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('slug', $slug)
            ->firstOrFail();

        $related = News::published()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('id', '!=', $article->id)
            ->where('news_type', $article->news_type)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('news.show', compact('article', 'related'));
    }
}
