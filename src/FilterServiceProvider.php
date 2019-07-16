<?php

namespace Panyanyany\LaravelExceptionFilter;

use Illuminate\Support\ServiceProvider;

class FilterServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            Illuminate\Contracts\Debug\ExceptionHandler::class,
            Handler::class
        );
    }
}
