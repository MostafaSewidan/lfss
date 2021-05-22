<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientsTable extends Migration {

	public function up()
	{
		Schema::create('clients', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('city_id')->nullable();
			$table->string('name')->nullable();
			$table->string('phone');
			$table->string('email')->nullable();
			$table->string('photo')->nullable();
			$table->string('pin_code')->nullable();
			$table->dateTime('pin_code_date_expired')->nullable();
			$table->string('password')->nullable();
			$table->string('api_token')->nullable();
			$table->tinyInteger('is_active')->default(0);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('clients');
	}
}