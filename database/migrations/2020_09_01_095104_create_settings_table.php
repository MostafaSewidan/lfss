<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration {

	public function up()
	{
		Schema::create('settings', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('settings_category_id');
			$table->string('key');
			$table->longText('value');
			$table->string('display_name');
			$table->enum('data_type', array('fileWithPreview','editor','textarea','number','email','date','text'));
			$table->integer('level')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('settings');
	}
}