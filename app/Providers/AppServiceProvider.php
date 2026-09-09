<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Show;
use App\Models\News;
use App\Models\Person;
use App\Models\Client;
use App\Observers\ShowObserver;
use App\Observers\NewsObserver;
use App\Observers\PersonObserver;
use App\Observers\ClientObserver;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Show::observe(ShowObserver::class);
        News::observe(NewsObserver::class);
        Person::observe(PersonObserver::class);
        Client::observe(ClientObserver::class);
    }
}
