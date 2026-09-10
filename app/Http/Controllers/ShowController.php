<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Category;

class ShowController extends Controller
{
    public function index()
    {
        $shows = Show::published()->latest('published_at')->paginate(12);
        $categories = Category::all();

        return view('shows.index', compact('shows', 'categories'));
    }
}
