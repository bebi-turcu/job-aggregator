<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\FeedClientContract;
use App\Services\Feeds\FeedService;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        FeedClientContract::class => FeedService::class,
    ];

    public function register(): void
    {
        $this->app->singleton(
            abstract: FeedClientContract::class,
            concrete: function () {
                return new FeedService();
            }
        );
    }
}
