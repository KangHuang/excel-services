<?php namespace App\Listeners;

class Statut  {

	/**
	 * Set the login user statut
	 * 
	 * @param  Illuminate\Auth\Events\Login $login
	 * @return void
	 */
	public function setLoginStatut($login)
	{
            if(session()->get('twoRole')=='use')
		session()->put('statut', $login->user->role->slug);
            else
                session()->put('statut', 'admin');
	}

	/**
	 * Set the visitor user statut
	 * 
	 * @return void
	 */
	public function setVisitorStatut()
	{
		session()->put('statut', 'visitor');
	}

	/**
	 * Set the statut
	 * 
	 * @return void
	 */
	public function setStatut()
	{
		if(!session()->has('statut')) 
		{
			session()->put('statut', auth()->check() ?  auth()->user()->role->slug : 'visitor');
		}
	}

}