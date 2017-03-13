<?php
namespace Arkitecht\Attributions\Database\Schema;

use Illuminate\Database\Schema\Blueprint as LaravelBlueprint;

class Blueprint extends LaravelBlueprint
{
    /**
     * Add attributions columns to the migration
     *
     * @param string $srcTable        The table the attributions should reference (default users)
     * @param string $srcKey          The column the attributions should reference (default id)
     * @param bool   $withSoftDeletes Include SoftDeletes in the migration? (default false)
     *
     * @return void
     */
    public function attributions($srcTable = 'users', $srcKey = 'id', $withSoftDeletes = false)
    {
        $this->integer('creator_id')->unsigned()->nullable()->references($srcTable)->on($srcKey);

        $this->integer('updater_id')->unsigned()->nullable()->references($srcTable)->on($srcKey);

        if ($withSoftDeletes) {
            $this->addDeleteAttribution($srcTable, $srcKey);
            $this->softDeletes();
        }
    }

    /**
     * Alias to attributions with $withSoftDeletes set to true
     *
     * @param string $srcTable The table the attributions should reference (default users)
     * @param string $srcKey   The column the attributions should reference (default id)
     *
     * @return void
     */
    public function attributionsWithSoftDeletes($srcTable = 'users', $srcKey = 'id')
    {
        $this->attributions($srcTable, $srcKey, true);
    }

    /**
     * Add the deleter attribution to a migration
     *
     * @param string $srcTable The table the attribution should reference (default users)
     * @param string $srcKey   The column the attribution should reference (default id)
     *
     * @return void
     */
    public function addDeleteAttribution($srcTable = 'users', $srcKey = 'id')
    {
        $this->integer('deleter_id')->unsigned()->nullable()->references($srcTable)->on($srcKey);
    }


    /**
     * Drop the attributions columns
     *
     * @return void
     */
    public function dropAttributions()
    {
        $this->dropColumn(['creator_id', 'updater_id']);
    }

    /**
     * Drop the deleter attribution column
     *
     * @return void
     */
    public function dropDeleteAttribution()
    {
        $this->dropColumn(['deleter_id']);
    }

    /**
     * Drop the attributions columns including deleter, and the softdeletes column
     *
     * @return void
     */
    public function dropAttributionsWithSoftDeletes()
    {
        $this->dropSoftDeletes();
        $this->dropColumn(['creator_id', 'updater_id', 'deleter_id']);
    }
}
