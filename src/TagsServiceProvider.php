<?php

namespace Ryanshowers\Tags;

use Illuminate\Support\ServiceProvider;

class TagsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //Include routes
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/routes.php';
        }

        $this->loadViewsFrom(__DIR__.'/resources/views', 'tags');
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'tags');
        
        //Migrations
        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('/migrations')
        ], 'migrations');
        
        $this->publishes([
            __DIR__.'/public/' => public_path('vendor/ryanshowers/tags'),
        ], 'public');
        
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/tags.php', 'tags');
    }
}
