<?php
namespace Arkitecht\Attributions\Database\Schema;

use Illuminate\Database\Schema\Blueprint as LaravelBlueprint;

class Blueprint extends LaravelBlueprint {
    public function attributions($srcTable='users',$srcKey='id') {
        $this->integer('creator_id')->unsigned()->nullable()->references($srcTable)->on($srcKey);

        $this->integer('updater_id')->unsigned()->nullable()->references($srcTable)->on($srcKey);
    }
}

?>