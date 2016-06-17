<?php

namespace Kidgifting\Emailage;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Illuminate\Support\ServiceProvider;
use Log;

class EmailageServiceProvider extends ServiceProvider
{
    /**
     * The console commands.
     *
     * @var bool
     */
    protected $commands = [
        'Kidgifting\Emailage\Commands\EmailageCommand'
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands($this->commands);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Kidgifting\Emailage\EmailageWrapper', function ($app) {

            /*
             * start oauth1
             */
            $stack = HandlerStack::create();

            $middleware = new Oauth1([
                'consumer_key'      => config('emailage.key'),
                'consumer_secret'   => config('emailage.secret'),
                'token_secret'      => false,
                'format'            => 'json'
            ]);

            $stack->push($middleware);
            /*
             * end oauth1
             */

            $client = new Client([
                'base_uri' => config('emailage.url'),
                'handler' => $stack,
                'auth' => 'oauth'
            ]);

            return new EmailageWrapper($client);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['emailage'];
    }
}