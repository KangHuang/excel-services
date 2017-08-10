<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('services', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('title', 255)->unique();
			$table->text('description');
                        $table->string('filename', 64)->unique();
                        $table->float('price')->unsigned();
			$table->boolean('seen')->default(false);
			$table->boolean('active')->default(false);
			$table->integer('provider_id')->unsigned();
                        $table->string('hid_fin', 512);
                        $table->string('hid_tec', 512);
		});

		Schema::table('services', function(Blueprint $table) {
			$table->foreign('provider_id')->references('id')->on('providers');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('services');
	}

}
