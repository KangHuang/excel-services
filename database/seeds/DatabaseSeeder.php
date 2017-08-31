<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role, App\Models\User, App\Models\Contact, App\Models\Provider, App\Models\Service, App\Models\Comment, App\Models\Relation, App\Models\UserRelation;
use App\Services\LoremIpsumGenerator;
use Illuminate\Support\Facades\Redis;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$lipsum = new LoremIpsumGenerator;

		Role::create([
			'title' => 'Manager',
			'slug' => 'manager'
		]);

		Role::create([
			'title' => 'Technical staff',
			'slug' => 'tec'
		]);

		Role::create([
			'title' => 'Financial staff',
			'slug' => 'fin'
		]);
                
                Role::create([
			'title' => 'Director',
			'slug' => 'dir'
		]);

		User::create([
			'username' => 'FirstManager',
			'email' => 'manager@la.fr',
			'password' => bcrypt('123'),
			'seen' => true,
			'role_id' => 1,
			'confirmed' => true
		]);

		User::create([
			'username' => 'FirstTechnical',
			'email' => 'tec@la.fr',
			'password' => bcrypt('123'),
			'seen' => true,
			'role_id' => 2,
			'valid' => true,
			'confirmed' => true
		]);

		User::create([
			'username' => 'FirstFinancial',
			'email' => 'fin@la.fr',
			'password' => bcrypt('123'),
			'role_id' => 3,
			'confirmed' => true
		]);
                
                User::create([
			'username' => 'FirstDirector',
			'email' => 'wenshangxi2016@hmail.com',
			'password' => bcrypt('123'),
			'role_id' => 4,
			'confirmed' => true
		]);
                
                User::create([
			'username' => 'SecondManager',
			'email' => 'sec@la.fr',
			'password' => bcrypt('123'),
			'role_id' => 1,
			'confirmed' => true
		]);
                
                Provider::create([
			'username' => 'FirstProvider',
			'email' => 'huangkang2016@gmail.com',
			'password' => bcrypt('123'),
			'confirmed' => true
		]);

		Contact::create([
			'name' => 'Peter',
			'email' => 'duiont@la.fr',
			'text' => 'Contact message created by Peter'
		]);

		Contact::create([
			'name' => 'Alice',
			'email' => 'duljiand@la.fr',
			'text' => 'message of alice'
		]);

		Contact::create([
			'name' => 'Tom',
			'email' => 'marljtin@la.fr',
			'text' => 'message of Tom',
			'seen' => true
		]);

		Service::create([
			'title' => 'Service demo',
			'description' => 'Test the first proprietary service',
			'filename' => 'demo.xlsx', 
                        'price' => 0.01,
			'active' => true,
			'provider_id' => 1,
                        'hid_tec' => 'A2,B2,C2',
                        'hid_fin' => 'A3,B3,C3',
		]);

		Comment::create([
			'content' => 'good service', 
			'user_id' => 2,
			'service_id' => 1
		]);

		Comment::create([
			'content' => 'nice one', 
			'user_id' => 2,
			'service_id' => 1
		]);

		Comment::create([
			'content' => 'want more services', 
			'user_id' => 3,
			'service_id' => 1
		]);
                
                Relation::create([
			'user_id' => 1,
			'service_id' => 1
		]);
                UserRelation::create([
			'manager_id' => 1,
			'staff_id' => 3,
		]);
                
                Redis::command('flushall');

	}

}
