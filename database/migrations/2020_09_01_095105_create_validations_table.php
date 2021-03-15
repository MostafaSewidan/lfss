<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateValidationsTable extends Migration {

	public function up()
	{
		Schema::create('validations', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('setting_id');
			$table->text('value');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('validations');
	}
}