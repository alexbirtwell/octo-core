<?php

namespace Octo;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Octo\Listeners\WelcomeUserNotification;
use Octo\Listeners\WelcomeUserQueuedNotification;
use Octo\Notifications\VerifyEmailQueued;
use Octo\Resources\Blade\Sidebar;
use Octo\Resources\Livewire\Notification\DropdownNotifications;
use Octo\Resources\Livewire\Notification\ListNotifications;
use Octo\Resources\Livewire\Subscribe;

class OctoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/octo.php' => config_path('octo.php'),
        ], 'octo-config');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'octo');

        // Octo blade
        Blade::component('octo::blade.sidebar', Sidebar::class, 'octo');
        Blade::component('octo::blade.hero','octo-hero');
        Blade::component('octo::blade.tile','octo-tile');

        // Need publish
        Blade::component('footer','footer');

        // Global
        Blade::component('octo::blade.global.action-link','action-link');
        Blade::component('octo::blade.global.bitbucket-icon','bitbucket-icon');
        Blade::component('octo::blade.global.connected-account','connected-account');
        Blade::component('octo::blade.global.github-icon','github-icon');
        Blade::component('octo::blade.global.gitlab-icon','gitlab-icon');
        Blade::component('octo::blade.global.facebook-icon','facebook-icon');
        Blade::component('octo::blade.global.facebook-icon','linked-in-icon');
        Blade::component('octo::blade.global.google-icon','google-icon');
        Blade::component('octo::blade.global.twitter-icon','twitter-icon');
        Blade::component('octo::blade.global.socialstream-providers','socialstream-providers');

        // Blade
        Livewire::component('octo-subscribe', Subscribe::class);
        Livewire::component('octo-dropdown-notifications', DropdownNotifications::class);
        Livewire::component('octo-list-notifications', ListNotifications::class);

        // Share views data
        View::share('sidebar',  ['items' => config('octo.navigation.sidebar')]);

        // Configure commmands
        $this->commands([
            Console\InstallCommand::class,
        ]);

        Route::group([
            'namespace' => 'Octo\Http\Controllers',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/octo.php');
        });
    }

    public function register()
    {
        if (Features::hasWelcomeUserFeatures()) {
            if (Features::queuedWelcomeUserNotifications()) {
                Event::listen(
                    Registered::class,
                    [WelcomeUserQueuedNotification::class, 'handle']
                );
            }

            if (!Features::queuedWelcomeUserNotifications()) {
                Event::listen(
                    Registered::class,
                    [WelcomeUserNotification::class, 'handle']
                );
            };
        }

        $this->mergeConfigFrom(
            __DIR__.'/../config/octo.php', 'octo'
        );
    }
}
