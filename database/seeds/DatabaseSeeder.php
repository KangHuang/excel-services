<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role, App\Models\User, App\Models\Contact, App\Models\Provider, App\Models\Service, App\Models\Comment, App\Models\Relation, App\Models\UserRelation;
use App\Services\LoremIpsumGenerator;

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
                
                Provider::create([
			'username' => 'FirstProvider',
			'email' => 'pro@la.fr',
			'password' => bcrypt('123'),
			'confirmed' => true
		]);

		Contact::create([
			'name' => 'Dupont',
			'email' => 'dupont@la.fr',
			'text' => 'Lorem ipsum inceptos malesuada leo fusce tortor sociosqu semper, facilisis semper class tempus faucibus tristique duis eros, cubilia quisque habitasse aliquam fringilla orci non. Vel laoreet dolor enim justo facilisis neque accumsan, in ad venenatis hac per dictumst nulla ligula, donec mollis massa porttitor ullamcorper risus. Eu platea fringilla, habitasse.'
		]);

		Contact::create([
			'name' => 'Durand',
			'email' => 'durand@la.fr',
			'text' => ' Lorem ipsum erat non elit ultrices placerat, netus metus feugiat non conubia fusce porttitor, sociosqu diam commodo metus in. Himenaeos vitae aptent consequat luctus purus eleifend enim, sollicitudin eleifend porta malesuada ac class conubia, condimentum mauris facilisis conubia quis scelerisque. Lacinia tempus nullam felis fusce ac potenti netus ornare semper molestie, iaculis fermentum ornare curabitur tincidunt imperdiet scelerisque imperdiet euismod.'
		]);

		Contact::create([
			'name' => 'Martin',
			'email' => 'martin@la.fr',
			'text' => 'Lorem ipsum tempor netus aenean ligula habitant vehicula tempor ultrices, placerat sociosqu ultrices consectetur ullamcorper tincidunt quisque tellus, ante nostra euismod nec suspendisse sem curabitur elit. Malesuada lacus viverra sagittis sit ornare orci, augue nullam adipiscing pulvinar libero aliquam vestibulum, platea cursus pellentesque leo dui. Lectus curabitur euismod ad, erat.',
			'seen' => true
		]);

		Service::create([
			'title' => 'Service demo',
			'description' => '<img alt="" src="/filemanager/userfiles/user2/rouge-shell.png" style="float:left; height:128px; width:128px" />' . $lipsum->getContent(50),
			'filename' => 'demo.xlsx', 
                        'price' => 16,
			'active' => true,
			'provider_id' => 1,
                        'hid_tec' => 'A2,B3',
                        'hid_fin' => 'no',
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

	}

}
