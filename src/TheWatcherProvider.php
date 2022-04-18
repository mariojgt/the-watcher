<?php

namespace Mariojgt\TheWatcher;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Mariojgt\TheWatcher\Commands\Install;
use Mariojgt\TheWatcher\Commands\Republish;
use Mariojgt\TheWatcher\Events\UserVerifyEvent;
use Mariojgt\TheWatcher\Listeners\SendUserVerifyListener;

class TheWatcherProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Event for when you create a new user
        Event::listen(
            UserVerifyEvent::class,
            [SendUserVerifyListener::class, 'handle']
        );

        // Load some commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                Republish::class,
                Install::class,
            ]);
        }
        // Load custom middlewares
        $this->middleware();

        // Load skeleton views
        $this->loadViewsFrom(__DIR__ . '/views', 'the-watcher');

        // Load the-watcher routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/admin.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/adminAuth.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/frontAuth.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        // Load Migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publish();
    }

    public function publish()
    {
        // Publish the public folder
        $this->publishes([
            __DIR__ . '/../Publish/Config/' => config_path(''),
        ]);

        // Publish the kernel stuff
        $this->publishes([
            __DIR__ . '/../Publish/InersiaRequest/kernel'
            => base_path('app/Http/'),
        ]);

        // Publish the inersia request stuff
        $this->publishes([
            __DIR__ . '/../Publish/InersiaRequest/handleRequest'
            => app_path('Http/Middleware'),
        ]);

        // Publish now view for the inersia were we goin to render the page
        $this->publishes([
            __DIR__ . '/../Publish/InersiaRequest/appLayout'
            => resource_path('views/the-watcher'),
        ]);

        // Publish the npm
        $this->publishes([
            __DIR__ . '/../Publish/Npm' => base_path(),
        ]);

        // Publish the resource
        $this->publishes([
            __DIR__ . '/../Publish/Resource' => resource_path('vendor/TheWatcher/'),
        ]);

        // Publish the public folder with the css and javascript pre compile
        $this->publishes([
            __DIR__ . '/../Publish/Public' => public_path('vendor/TheWatcher/'),
        ]);
    }


    /**
     * Load some custom middlerwhere
     * @return [type]
     */
    public function middleware()
    {
        $this->app['router']->aliasMiddleware(
            'skeleton_admin',
            \Mariojgt\TheWatcher\Middleware\TheWatcher::class
        );

        $this->app['router']->aliasMiddleware(
            'skeleton_guest',
            \Mariojgt\TheWatcher\Middleware\SkeletonGuest::class
        );

        // Loading Custom middlewhere
        $this->app['router']->aliasMiddleware(
            'boot_token',
            \Mariojgt\TheWatcher\Middleware\BootTokenApi::class
        );
    }
}
