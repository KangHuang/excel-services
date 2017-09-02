<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Bus\Dispatcher as BusDispatcher;
use App\Events\UserAccess;

class App
{

	/**
	 * The command bus.
	 *
	 * @array $bus
	 */
	protected $bus;

	/**
	 * Create a new App instance.
	 *
	 * @param  Illuminate\Bus\Dispatcher $bus
	 * @return void
	*/
	public function __construct(
		BusDispatcher $bus
		)
	{
		$this->bus = $bus;
		
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  Illuminate\Http\Request  $request
	 * @param  Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		

		event(new UserAccess);

		return $next($request);
	}

}
