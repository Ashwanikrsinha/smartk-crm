<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Company;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(180);

        View::share('bearer_token', 'Bearer password');
        View::share('company', null);

        View::composer('*', function ($view) {
            static $cachedCompany = false;

            if ($cachedCompany === false) {
                try {
                    $cachedCompany = Company::first();
                } catch (\Throwable $e) {
                    $cachedCompany = null;
                }
            }

            if (! array_key_exists('company', $view->getData())) {
                $view->with('company', $cachedCompany);
            }
        });
    }
}
