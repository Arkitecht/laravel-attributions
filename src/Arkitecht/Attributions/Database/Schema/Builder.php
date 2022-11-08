<?php

namespace Arkitecht\Attributions\Database\Schema;

use Illuminate\Database\Connection;

class Builder extends \Illuminate\Database\Schema\Builder
{
    /**
     * Create a new database Schema manager.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @return void
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->grammar = $connection->getSchemaGrammar();

        $this->blueprintResolver(function ($table, $callback) {
            return new Blueprint($table, $callback);
        });
    }
}
