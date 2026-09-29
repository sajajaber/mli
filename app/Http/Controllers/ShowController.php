<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Category;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category');

        $query = Show::published()
            ->with('category')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($categorySlug) {
            $query->whereHas('category', function ($categoryQuery) use ($categorySlug) {
                $categoryQuery->where('slug', $categorySlug);
            });
        }

        $shows = $query->paginate(12)->withQueryString();
        $categories = Category::query()
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
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return view('shows.show', compact('show', 'relatedShows'));
    }
}
