<?php
namespace Arkitecht\Attributions\Facades;

use Arkitecht\Attributions\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Facade;

class Schema extends Facade
{
    /**
     * Get a schema builder instance for a connection.
     *
     * @param  string  $name
     * @return \Illuminate\Database\Schema\Builder
     */
    public static function connection($name)
    {
        $schema = static::$app['db']->connection($name)->getSchemaBuilder();
		$schema->blueprintResolver(function($table, $callback) {
          return new Blueprint($table, $callback);
        });
		return $schema;
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        $schema = static::$app['db']->connection()->getSchemaBuilder();
		$schema->blueprintResolver(function($table, $callback) {
          return new Blueprint($table, $callback);
        });
		return $schema;
    }
}
