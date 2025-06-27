<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        /*
         * Powiązanie interfejsu repozytorium z jego implementacją.
         * Dzięki temu można wstrzykiwać TaskRepositoryInterface,
         * a kontener dostarczy instancję TaskRepository.
         */
        $this->app->bind(
            \App\Repositories\Contracts\TaskRepositoryInterface::class,
            \App\Repositories\Eloquent\TaskRepository::class,
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
} 