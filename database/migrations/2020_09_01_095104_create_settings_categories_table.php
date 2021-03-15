<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsCategoriesTable extends Migration {

	public function up()
	{
		Schema::create('settings_categories', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->integer('level')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('settings_categories');
	}
}