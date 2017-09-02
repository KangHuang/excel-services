<?php namespace App\Http\Controllers;

use App\Repositories\UserRepository,App\Repositories\RoleRepository;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\RoleRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller {

	/**
	 * The UserRepository instance.
	 *
	 * @var App\Repositories\UserRepository
	 */
	protected $user_handler;

	/**
	 * The RoleRepository instance.
	 *
	 * @var App\Repositories\RoleRepository
	 */	
	protected $role_handler;

        
	/**
	 * Create a new UserController instance.
	 *
	 * @param  App\Repositories\UserRepository $user_handler
	 * @param  App\Repositories\RoleRepository $role_handler
	 * @return void
	 */
	public function __construct(
		UserRepository $user_handler,
		RoleRepository $role_handler)
	{
		$this->user_handler = $user_handler;
		$this->role_handler = $role_handler;

		$this->middleware('manager');
	}

	/**
	 * Display a listing of own staff
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->indexSort('total');
	}

	/**
	 * Display a listing of a manager's staff
	 *
     * @param  string  $role
	 * @return Response
	 */
	public function indexSort($role)
	{
		$users = auth()->guard('users')->user()->staff()->paginate(8); 
		$links = $users->render();
		$roles = $this->role_handler->all();

		return view('back.users.index', compact('users', 'links', 'roles'));		
	}

	/**
	 * form for creating a staff.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('back.users.create', $this->role_handler->getAllSelect());
	}

	/**
	 * save a created staff
	 *
	 * @param  App\requests\UserCreateRequest $request
	 *
	 * @return Response
	 */
	public function store(
		UserCreateRequest $request)
	{
		$user = $this->user_handler->store($request->all());

                if(isset($_POST['relation'])){
                    auth()->guard('users')->user()->staff()->attach($user->id);
                }

		return redirect('user/show')->with('ok', trans('back/users.created'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  App\Models\User
	 * @return Response
	 */
	public function show(User $user)
	{
		return view('back.users.show',  compact('user'));
	}
        
        /**
	 * Remove the staff if a manager has the relationship
	 *
	 * @param  App\Models\user $user
	 * @return Response
	 */
	public function destroyStaff($staff_id)
	{
            
            
             if(auth()->guard('users')->user()->staff()->get()->contains($staff_id)){
                 
                auth()->guard('users')->user()->staff()->detach($staff_id);
                $user = $this->user_handler->getById($staff_id);
		$this->user_handler->destroyUser($user);
		return redirect('user/show')->with('ok', trans('back/users.destroyed'));
             }
             return redirect('user/show')->with('error', trans('back/users.destroy-fail'));             
             
	}
        
}
