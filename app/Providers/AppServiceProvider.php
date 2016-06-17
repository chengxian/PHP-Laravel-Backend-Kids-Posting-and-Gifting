<?php

namespace App\Providers;

use App\KFMail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() == 'local') {
            $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');

            //IDE helper
            $this->app->register('Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider');

            $this->app->register('JeroenG\Packager\PackagerServiceProvider');

            // migration genrator
//            $this->app->register('Way\Generators\GeneratorsServiceProvider');
//            $this->app->register('Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider');
        }



        // Mandril Wrapper
        $this->app->bind('App\KFMail', function($app) {
            return new KFMail($app['Weblee\Mandrill\Mail']);
        });
    }
}
