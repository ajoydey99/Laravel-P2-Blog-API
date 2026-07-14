<?php
namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // any route using {tag} or {post}
        // automatically requires digits
        Route::pattern('tag', '[0-9]+');
        Route::pattern('post', '[0-9]+');
        Route::pattern('user', '[0-9]+');
    }
}