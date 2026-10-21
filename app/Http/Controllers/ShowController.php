<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Show;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $this->slugFromQuery($request, 'category');
        $genreSlug = $this->slugFromQuery($request, 'genre');

        $query = Show::published()
            ->with(['categories', 'genres'])
            ->orderByDesc('published_at')
            ->orderBy('title_en')
            ->orderByDesc('id');

        if ($categorySlug) {
            $query->whereHas('categories', function ($categoryQuery) use ($categorySlug) {
                $categoryQuery->where('slug', $categorySlug);
            });
        }

        if ($genreSlug) {
            $query->whereHas('genres', function ($genreQuery) use ($genreSlug) {
                $genreQuery->where('slug', $genreSlug);
            });
        }

        $shows = $query->paginate(12)->withQueryString();

        $categories = Category::query()
            ->select(['id', 'name_en', 'name_ar', 'slug'])
            ->whereHas('shows', fn($query) => $query->published())
            ->orderBy('name_en')
            ->get();

        // Genre filters are shown only once a category is chosen, and only for that category.
        $genres = collect();

        if ($categorySlug) {
            $selectedCategory = Category::query()->where('slug', $categorySlug)->first();

            if ($selectedCategory) {
                $genres = Genre::query()
                    ->select(['id', 'category_id', 'name_en', 'name_ar', 'slug'])
                    ->where('category_id', $selectedCategory->id)
                    ->whereHas('shows', fn($query) => $query->published())
                    ->orderBy('name_en')
                    ->get();
            }
        }

        return view('shows.index', compact('shows', 'categories', 'genres', 'categorySlug', 'genreSlug'));
    }

    public function category(Request $request, string $slug)
    {
        // Find the requested category or return a 404 page.
        $category = Category::query()
            ->where('slug', $slug)
            ->firstOrFail();

        // Reuse the existing filtering logic for this category.
        $request->query->set('category', $slug);

        return $this->index($request);
    }

    public function show(string $slug)
    {
        $show = Show::published()
            ->with(['categories', 'genres'])
            ->where('slug', $slug)
            ->firstOrFail();

        $categoryIds = $show->categories->pluck('id');

        $relatedShows = Show::published()
            ->with('categories')
            ->where('id', '!=', $show->id)
            ->when($categoryIds->isNotEmpty(), function ($query) use ($categoryIds) {
                $query->whereHas('categories', fn($categoryQuery) => $categoryQuery->whereIn('categories.id', $categoryIds));
            })
            ->orderByDesc('published_at')
            ->orderBy('title_en')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return view('shows.show', compact('show', 'relatedShows'));
    }

    private function slugFromQuery(Request $request, string $key): ?string
    {
        $value = $request->query($key);

        return is_string($value) && $value !== '' ? $value : null;
    }
}
