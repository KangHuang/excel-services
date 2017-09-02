<?php

namespace App\Repositories;

use App\Models\User, App\Models\Role, Illuminate\Support\Facades\Log;

class UserRepository extends BaseRepository
{

	/**
	 * The Role instance.
	 *
	 * @var App\Models\Role
	 */	
	protected $role;
        
        /**
	 * The is permitted to a service
	 *
	 * @var int
	 */	
	protected $isPermit;

	/**
	 * Create a new UserRepository instance.
	 *
   	 * @param  App\Models\User $user
	 * @param  App\Models\Role $role
	 * @return void
	 */
	public function __construct(
		User $user, 
		Role $role)
	{
		$this->model = $user;
		$this->role = $role;
                $isPermit = 0;
	}

	/**
	 * Save the User.
	 *
	 * @param  App\Models\User $user
	 * @param  Array  $inputs
	 * @return void
	 */
  	private function save($user, $inputs)
	{		
		if(isset($inputs['seen'])) 
		{
			$user->seen = $inputs['seen'] == 'true';		
		} else {

			$user->username = $inputs['username'];
			$user->email = $inputs['email'];
//			$role_user = $this->role->where('slug', 'manager')->first();
			$user->role_id = $inputs['role_id'];
			
		}

		$user->save();
	}

	/**
	 * Get users collection paginate.
	 *
	 * @param  int  $n
	 * @param  string  $role
	 * @return Illuminate\Support\Collection
	 */
	public function index($n, $role)
	{
		if($role != 'total')
		{
			return $this->model
			->with('role')
			->whereHas('role', function($q) use($role) {
				$q->whereSlug($role);
			})		
			->oldest('seen')
			->latest()
			->paginate($n);			
		}

		return $this->model
		->with('role')		
		->oldest('seen')
		->latest()
		->paginate($n);
	}

	/**
	 * Count the users.
	 *
	 * @param  string  $role
	 * @return int
	 */
	public function count($role = null)
	{
		if($role)
		{
			return $this->model
			->whereHas('role', function($q) use($role) {
				$q->whereSlug($role);
			})->count();			
		}

		return $this->model->count();
	}

	/**
	 * Count the users.
	 *
	 * @param  string  $role
	 * @return int
	 */
	public function counts()
	{
		$counts = [
			'admin' => $this->count('admin'),
			'redac' => $this->count('redac'),
			'user' => $this->count('user')
		];

		$counts['total'] = array_sum($counts);

		return $counts;
	}

	/**
	 * Create a user.
	 *
	 * @param  array  $inputs
	 * @param  int    $confirmation_code
	 * @return App\Models\User 
	 */
	public function store($inputs, $confirmation_code = null)
	{
		$user = new $this->model;

		$user->password = bcrypt($inputs['password']);

		if($confirmation_code) {
			$user->confirmation_code = $confirmation_code;
		} else {
			$user->confirmed = true;
		}

		$this->save($user, $inputs);

		return $user;
	}

	/**
	 * Get statut of authenticated user.
	 *
	 * @return string
	 */
	public function getStatut()
	{
		return session('statut');
	}

	/**
	 * Valid user.
	 *
     * @param  bool  $valid
     * @param  int   $id
	 * @return void
	 */
	public function valid($valid, $id)
	{
		$user = $this->getById($id);

		$user->valid = $valid == 'true';

		$user->save();
	}

	/**
	 * Destroy a user.
	 *
	 * @param  App\Models\User $user
	 * @return void
	 */
    public function destroyUser(User $user)
    {
        $user->comments()->delete();
        
        $user->delete();
    }

	/**
	 * Confirm a user.
	 *
	 * @param  string  $confirmation_code
	 * @return App\Models\User
	 */
	public function confirm($confirmation_code)
	{
		$user = $this->model->whereConfirmationCode($confirmation_code)->firstOrFail();

		$user->confirmed = true;
		$user->confirmation_code = null;
		$user->save();
	}

}
