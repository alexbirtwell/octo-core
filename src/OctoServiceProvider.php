<?php

namespace Octo;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Octo\Resources\Blade\PhoneInput;
use Octo\Resources\Blade\Sidebar;
use Octo\Resources\Livewire\Notifications\DropdownNotifications;
use Octo\Resources\Livewire\Notifications\ListNotifications;
use Octo\Resources\Livewire\Subscribe;
use Octo\Billing\BillingServiceProvider;
use Octo\Common\CommonServiceProvider;
use Octo\Resources\Livewire\System\ListUsers;
use Octo\Resources\Livewire\SwitchDashboard;
use Octo\Resources\Livewire\System\SiteFooter;
use Octo\Resources\Livewire\System\SiteInfo;
use Octo\Resources\Livewire\System\SiteSection;
use Octo\Resources\Livewire\System\SiteSections;
use Octo\Settings\SettingServiceProvider;
use Octo\System\SystemServiceProvider;

class OctoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'octo');

        $this->publishes([
            __DIR__.'/../config/octo.php' => config_path('octo.php'),
        ], 'octo-config');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'octo');

        // Register Blade components
        Blade::component(Sidebar::class, 'octo-sidebar');
        Blade::component(PhoneInput::class, 'octo-phone-input');
        Blade::component('octo::components.tile', 'octo-tile');
        Blade::component('octo::components.card-count', 'octo-card-count');
        Blade::component('octo::components.slide-over', 'octo-slide-over');
        Blade::component('footer', 'footer');

        // Register Livewire components
        Livewire::component('octo-subscribe', Subscribe::class);
        Livewire::component('octo-dropdown-notifications', DropdownNotifications::class);
        Livewire::component('octo-list-notifications', ListNotifications::class);

        // Configure commmands
        $this->commands([
            Console\InstallCommand::class,
            Console\InstallSmsDriverCommand::class,
            Console\UninstallSmsDriverCommand::class,
            Console\SetupDemoCommand::class,
        ]);

        // Configure routes
        $this->loadRoutesFrom(__DIR__.'/../routes/octo.php');
    }

    public function register()
    {
        $this->app->register(SettingServiceProvider::class);
        $this->app->register(SystemServiceProvider::class);
        $this->app->register(CommonServiceProvider::class);
        $this->app->register(BillingServiceProvider::class);
        $this->app->register(MenuServiceProvider::class);
        $this->mergeConfigFrom(__DIR__.'/../config/octo.php', 'octo');
        $this->mergeConfigFrom(__DIR__.'/../config/services.php', 'services');
    }
}
