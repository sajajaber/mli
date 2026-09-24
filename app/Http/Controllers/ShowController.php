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
            ->latest('published_at');

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
}
