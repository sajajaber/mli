<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            App::setLocale('en');
        } else {
            App::setLocale(session('locale', config('app.locale', 'ar')));
        }

        return $next($request);
    }
}
