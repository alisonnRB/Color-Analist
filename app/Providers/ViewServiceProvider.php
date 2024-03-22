<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Não é necessário registrar nada para uma aplicação API
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        // Não é necessário fazer nada para uma aplicação API
    }
}