<?php

namespace Academe\Laravel;

use Academe\Contracts\Academe;
use Academe\Contracts\ConditionMaker;
use Academe\Contracts\InstructionMaker;
use Academe\Contracts\Writer;
use Illuminate\Support\ServiceProvider;

class AcademeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $configPath = __DIR__ . '/../../config/academe.php';

        $this->publishes([
            $configPath => config_path('academe.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../../config/academe.php';

        $this->mergeConfigFrom($configPath, 'academe');

        $this->app->singleton(Academe::class, function ($app) {
            /**
             * @var $app \Illuminate\Foundation\Application
             */
            return \Academe\Academe::initialize($app->config['academe']);
        });

        $this->app->singleton(Writer::class, function ($app) {
            /**
             * @var $app     \Illuminate\Foundation\Application
             * @var $academe Academe
             */

            $academe = $app->make(Academe::class);

            return $academe->getWriter();
        });
    }
}

