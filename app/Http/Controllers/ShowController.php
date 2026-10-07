<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Show;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category');

        $query = Show::published()
            ->with('category')
            ->orderByDesc('published_at')
            ->orderBy('title_en')
            ->orderByDesc('id');

        if ($categorySlug) {
            $query->whereHas('category', function ($categoryQuery) use ($categorySlug) {
                $categoryQuery->where('slug', $categorySlug);
            });
        }

        $shows = $query->paginate(12)->withQueryString();

        $categories = Category::query()
            ->select(['id', 'name_en', 'name_ar', 'slug'])
            ->whereHas('shows', fn ($query) => $query->published())
            ->orderBy('name_en')
            ->get();

        return view('shows.index', compact('shows', 'categories', 'categorySlug'));
    }

    public function show(string $slug)
    {
        $show = Show::published()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedShows = Show::published()
            ->with('category')
            ->where('id', '!=', $show->id)
            ->when($show->category_id, function ($query) use ($show) {
                $query->where('category_id', $show->category_id);
            })
            ->orderByDesc('published_at')
            ->orderBy('title_en')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return view('shows.show', compact('show', 'relatedShows'));
    }
}
