<?php

namespace Academe\Laravel;

use Academe\Contracts\Academe;
use Academe\Contracts\Writer;
use Illuminate\Support\ServiceProvider;
use Academe\Laravel\Console as AcademeConsole;

class AcademeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $configPath = __DIR__ . '/../../config/academe.php';

        if (function_exists('config_path')) {
            $publishPath = config_path('academe.php');
        } else {
            $publishPath = base_path('config/academe.php');
        }

        $this->publishes([$configPath => $publishPath], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
        $this->registerCommands();

        $this->app->singleton(Academe::class, function ($app) {
            return \Academe\Academe::initialize($app->config['academe']);
        });

        $this->app->singleton(Writer::class, function ($app) {
            return $app->make(Academe::class)->getWriter();
        });

        $this->app->bind('academe', Academe::class);
        $this->app->bind('academe.writer', Writer::class);
    }

    /**
     *
     */
    protected function mergeConfig()
    {
        $configPath = __DIR__ . '/../../config/academe.php';

        $this->mergeConfigFrom($configPath, 'academe');
    }

    /**
     * Register all custom commands.
     */
    protected function registerCommands()
    {
        $this->commands([
            AcademeConsole\BlueprintMakeCommand::class,
            AcademeConsole\BondMakeCommand::class,
        ]);
    }
}

