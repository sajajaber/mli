<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');

        $query = News::published()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at');

        if (in_array($type, ['mli_news', 'media_news'], true)) {
            $query->where('news_type', $type);
        } else {
            $type = null;
        }

        $news = $query->paginate(9)->withQueryString();

        $featuredQuery = News::published()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at');

        if ($type) {
            $featuredQuery->where('news_type', $type);
        }

        $featured = $featuredQuery->first();

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
