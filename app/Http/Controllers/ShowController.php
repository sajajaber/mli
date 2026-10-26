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

        $selectedCategory = $categorySlug
            ? Category::query()
                ->with([
                    'children:id,parent_id',
                    'parent:id,parent_id,slug',
                ])
                ->where('slug', $categorySlug)
                ->first()
            : null;

        // A parent category includes shows directly assigned to it and to any child.
        $categoryIds = $selectedCategory
            ? collect([$selectedCategory->id])
                ->merge($selectedCategory->children->pluck('id'))
                ->unique()
                ->values()
            : collect();

        $query = Show::published()
            ->with(['categories.parent', 'genres'])
            ->orderByDesc('published_at')
            ->orderBy('title_en')
            ->orderByDesc('id');

        if ($categorySlug) {
            $query->whereHas('categories', fn ($categoryQuery) => $categoryQuery
                ->whereIn('categories.id', $categoryIds));
        }

        if ($genreSlug) {
            $query
                ->where('content_type', 'series')
                ->whereHas('genres', fn ($genreQuery) => $genreQuery->where('slug', $genreSlug));
        }

        $shows = $query->paginate(12)->withQueryString();

        // Parents and children remain visible even with no assigned/published shows.
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with([
                'children' => fn ($query) => $query
                    ->select(['id', 'parent_id', 'name_en', 'name_ar', 'slug'])
                    ->orderBy('name_en'),
            ])
            ->orderBy('name_en')
            ->get();

        // Genres are reserved for Series content and are maintained under the top-level Series category.
        $genres = collect();

        $selectedCategoryIsSeries = $selectedCategory && (
            $selectedCategory->slug === 'series'
            || $selectedCategory->parent?->slug === 'series'
        );

        if ($selectedCategoryIsSeries) {
            $genres = Genre::query()
                ->select(['id', 'category_id', 'name_en', 'name_ar', 'slug'])
                ->whereHas('category', fn ($categoryQuery) => $categoryQuery
                    ->whereNull('parent_id')
                    ->where('slug', 'series'))
                ->whereHas('shows', function ($showQuery) use ($categoryIds) {
                    $showQuery
                        ->published()
                        ->where('content_type', 'series')
                        ->whereHas('categories', fn ($categoryQuery) => $categoryQuery
                            ->whereIn('categories.id', $categoryIds));
                })
                ->orderBy('name_en')
                ->get();
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
            ->with(['categories.parent', 'genres', 'castMembers'])
            ->where('slug', $slug)
            ->firstOrFail();

        $categoryIds = $show->effective_categories->pluck('id');

        $relatedShows = Show::published()
            ->with(['categories.parent'])
            ->where('id', '!=', $show->id)
            ->when($categoryIds->isNotEmpty(), function ($query) use ($categoryIds) {
                $query->whereHas('categories', fn ($categoryQuery) => $categoryQuery
                    ->whereIn('categories.id', $categoryIds)
                    ->orWhereIn('categories.parent_id', $categoryIds));
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
