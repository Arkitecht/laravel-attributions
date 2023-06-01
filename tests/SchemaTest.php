<?php

use Arkitecht\Attributions\Database\Schema\Blueprint;
use Arkitecht\Attributions\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SchemaTest extends Orchestra\Testbench\TestCase
{
    /** @test */
    function can_create_migration_with_attributions()
    {
        $schema = DB::connection()->getSchemaBuilder();
        $schema->blueprintResolver(function($table, $callback) {
            return new Blueprint($table, $callback);
        });

        $schema->create('posts', function($table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('title');
            $table->text('body');
            $table->attributions();
            $table->timestamps();
        });

        $columns = Schema::getColumnListing('posts');
        $this->assertTrue(in_array('creator_id', $columns));
        $this->assertTrue(in_array('updater_id', $columns));
    }

    /** @test */
    function can_create_migration_with_attributions_and_deletes()
    {
        $schema = DB::connection()->getSchemaBuilder();
        $schema->blueprintResolver(function($table, $callback) {
            return new Blueprint($table, $callback);
        });

        $schema->create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('title');
            $table->text('body');
            $table->attributionsWithSoftDeletes();
            $table->timestamps();
        });

        $columns = Schema::getColumnListing('posts');
        $this->assertTrue(in_array('creator_id', $columns));
        $this->assertTrue(in_array('updater_id', $columns));
        $this->assertTrue(in_array('deleter_id', $columns));
        $this->assertTrue(in_array('deleted_at', $columns));
    }

}
