<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLostsTable extends Migration {

	public function up()
	{
		Schema::create('losts', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('client_id');
			$table->integer('city_id');
			$table->integer('category_id');
			$table->string('name');
			$table->text('description')->nullable();
			$table->string('photo')->nullable();
			$table->enum('type', array('lost', 'found'));
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('losts');
	}
}