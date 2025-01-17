<?php

namespace Octo;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Octo\Billing\BillingServiceProvider;
use Octo\Common\CommonServiceProvider;
use Octo\Marketing\MarketingServiceProvider;
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

        $this->commands([
            Console\InstallSmsDriverCommand::class,
            Console\UninstallSmsDriverCommand::class,
            Console\SetupDemoCommand::class,
            Console\InstallCommand::class,
        ]);

        $this->loadRoutesFrom(__DIR__.'/../routes/octo.php');

        Filament::serving(function (): void {
            Filament::registerTheme(mix('css/app.css'));

            Filament::registerNavigationGroups([
                'Marketing',
                'Settings',
            ]);
        });

        // TODO: Add Blade directives for the following: using feature package. It is an workaround.
        Blade::if('feature', function (string $feature, $applyIfOn = true) {
            return $applyIfOn
                ? feature($feature)
                : ! feature($feature);
        });
    }

    public function register()
    {
        $this->app->register(SystemServiceProvider::class);
        $this->app->register(CommonServiceProvider::class);

        if_feature_is_enabled('billing', function () {
            $this->app->register(BillingServiceProvider::class);
        });

        $this->app->register(MenuServiceProvider::class);
        $this->app->register(CommonServiceProvider::class);
        $this->app->register(MarketingServiceProvider::class);
        $this->mergeConfigFrom(__DIR__.'/../config/octo.php', 'octo');
        $this->mergeConfigFrom(__DIR__.'/../config/services.php', 'services');
    }
}
