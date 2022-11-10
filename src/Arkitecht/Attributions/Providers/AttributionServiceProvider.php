<?php

namespace Arkitecht\Attributions\Providers;

use Arkitecht\Attributions\Database\Schema\Blueprint;
use Arkitecht\Attributions\Database\Schema\Builder;
use Arkitecht\Attributions\Traits\Attributions;
use Illuminate\Support\ServiceProvider;

class AttributionServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('db.schema', function ($app) {
            $builder = $app['db']->connection()->getSchemaBuilder();
            $builder->blueprintResolver(function ($table, $callback) {
                return new Blueprint($table, $callback);
            });

            return $builder;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Blueprint::class,
            Builder::class,
            Attributions::class,
        ];
    }
}
