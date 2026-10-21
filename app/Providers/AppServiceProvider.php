<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ->links() renders Bootstrap markup, which the admin theme styles. The
        // default Tailwind markup draws its arrow icons full-size without Tailwind.
        Paginator::useBootstrapFour();
    }
}
