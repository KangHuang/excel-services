<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use App\Repositories\ServiceRepository;

class IsPermit {

    /**
     * The ServiceRepository instance.
     *
     * @var App\Repositories\ServiceRepository
     */
    protected $service_gestion;

    /**
     * Create a new ContactController instance.
     *
     * @return void
     */
    public function __construct(ServiceRepository $service_gestion) {
        $this->service_gestion = $service_gestion;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        
        //if manager is permitted
        if (session('statut') === 'manager') {
            $user_id = auth()->guard('users')->id();
            $usersPermit = $this->service_gestion->getById($request->service_id)->users()->get();
            if ($usersPermit->contains($user_id))
                return $next($request);
            else
                return redirect('/service/payment/'.$request->service_id);
        }
        
        //If a staff's manager is permitted
        else if (session('statut') === 'tec' || session('statut') === 'fin') {
            $managers = auth()->guard('users')->user()->managers()->get();
            $usersPermit = $this->service_gestion->getById($request->service_id)->users()->get();
            foreach ($managers as $manager){
                if($usersPermit->contains($manager->id))
                    return $next($request);
                else
                    return redirect('/services')->with('error', 'Sorry, you do not have access to this service');
            }
        }
        
        else if(session('statut') === 'admin'){
            $services = auth()->guard('providers')->user()->services()->get();
            if($services->contains($request->service_id))
                return $next($request);
            else
                return redirect('/services')->with('error', 'Sorry, you do not have access to this service');
                
        }
        return redirect('/services')->with('error', 'Please log in');
    }

}
