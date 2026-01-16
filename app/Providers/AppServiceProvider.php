<?php

namespace App\Providers;

use App\Contracts\ActorExtractorInterface;
use App\Repositories\ActorRepositoryInterface;
use App\Repositories\ActorRepository;
use App\Services\OpenAiActorExtractor;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ActorRepositoryInterface::class, ActorRepository::class);
        $this->app->bind(ActorExtractorInterface::class, OpenAiActorExtractor::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
