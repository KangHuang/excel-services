<?php   

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comments', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->text('content');
			$table->boolean('seen')->default(true);
			$table->integer('user_id')->unsigned();
			$table->integer('service_id')->unsigned();
		});

		Schema::table('comments', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('service_id')->references('id')->on('services');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{	

		Schema::drop('comments');
	}

}
