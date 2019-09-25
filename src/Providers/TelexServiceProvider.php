<?php

namespace Telex\Providers;

use Telex\TelexClient;

use Illuminate\Support\ServiceProvider;

class TelexServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('Telex', function ($app) {
            $organizationKey = config('services.telex.organization_key');

            return new TelexClient($organizationKey);
        });
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
