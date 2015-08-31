Laravel Attributions
=================
[![Laravel 5.1](https://img.shields.io/badge/Laravel-5.1-orange.svg?style=flat-square)](http://laravel.com)
[![Source](http://img.shields.io/badge/source-arkitecht/laravel-attributions-blue.svg?style=flat-square)](https://github.com/arkitecht/laravel-attributions)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

Easily attribute the creator / last updater of a model in your database.

Designed to work like (and alongside) $table->timestamps() in your Laravel migrations, Attributions introduces $table->attributions(). This will add creator_id and updater_id columns to your table to track the user that created and updated the model respectively.  By default this uses the Laravel 5.1 predefined users table, but can be customized to reference any table and key combination.

Quick Installation
------------------
You can install the package most easily through composer

#### Laravel 5.1.x
```
composer require arkitecht/laravel-attributions
```

Schema Blueprint and Facades
------------------
Once this operation is complete, simply you can update the Schema Facade to point to our drop-in replacement, which uses our Blueprint extension class to add the attributions.

#### Laravel 5.1.x

##### Facade
```php
~~'Schema'    => Illuminate\Support\Facades\Schema::class,~~
'Schema' 	=> Arkitecht\Attributions\Facades\Schema::class,
```

You can also manually use the attributions builder, without overwriting the Facade like so:

```php
/**
 * Run the migrations.
 *
 * @return void
 */
public function up()
{
    $schema =  DB::getSchemaBuilder();

    $schema->blueprintResolver(function($table, $callback) {
        return new Blueprint($table, $callback);
    });

    $schema->create('tests', function (Blueprint $table) {
        $table->increments('id');
        $table->timestamps();
        $table->attributions();
    });
}
```

Using it in your model
------------------
To have the creator and updater automagically updated when a model is created and updated, just use the Attributions trait in your model.

```php
<?php

namespace App;

use Arkitecht\Attributions\Traits\Attributions;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use Attributions;
}
?>
```