<?php   

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRelationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_relations', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('manager_id')->unsigned();
			$table->integer('staff_id')->unsigned()->unique();
		});

		Schema::table('user_relations', function(Blueprint $table) {
			$table->foreign('manager_id')->references('id')->on('users');
			$table->foreign('staff_id')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{	

		Schema::drop('user_relations');
	}

}
