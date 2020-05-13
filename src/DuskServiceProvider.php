<?php

namespace Laravel\Dusk;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->environment('production')) {
            Route::group([
                'prefix' => config('dusk.path'),
                'domain' => config('dusk.domain', null),
                'middleware' => 'web',
            ], function () {
                Route::get('/login/{userId}/{guard?}', [
                    'uses' => 'Laravel\Dusk\Http\Controllers\UserController@login',
                    'as' => 'dusk.login',
                ]);

                Route::get('/logout/{guard?}', [
                    'uses' => 'Laravel\Dusk\Http\Controllers\UserController@logout',
                    'as' => 'dusk.logout',
                ]);

                Route::get('/user/{guard?}', [
                    'uses' => 'Laravel\Dusk\Http\Controllers\UserController@user',
                    'as' => 'dusk.user',
                ]);
            });
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function register()
    {
        if (! $this->app->environment('production')) {
            $this->mergeConfigFrom(__DIR__.'/../config/dusk.php', 'dusk');
            $this->publishes([
                __DIR__.'/../config/dusk.php' => config_path('dusk.php'),
            ], 'dusk-config');

            if ($this->app->runningInConsole()) {
                $this->commands([
                    Console\InstallCommand::class,
                    Console\DuskCommand::class,
                    Console\DuskFailsCommand::class,
                    Console\MakeCommand::class,
                    Console\PageCommand::class,
                    Console\ComponentCommand::class,
                    Console\ChromeDriverCommand::class,
                ]);
            }
        }
    }
}
