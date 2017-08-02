<?php namespace App\Http\Controllers;

use App\Repositories\ContactRepository;
use App\Repositories\ProviderRepository;
use App\Repositories\CommentRepository;
use App\Repositories\ServiceRepository;

class AdminController extends Controller {

    /**
     * The UserRepository instance.
     *
     * @var App\Repositories\ProviderRepository
     */
    protected $provider_gestion;

    /**
     * Create a new AdminController instance.
     *
     * @param  App\Repositories\UserRepository $provider_gestion
     * @return void
     */
    public function __construct(ProviderRepository $provider_gestion)
    {
		$this->provider_gestion = $provider_gestion;
    }

	/**
	* Show the admin panel.
	*
	* @param  App\Repositories\ContactRepository $contact_gestion
	* @param  App\Repositories\ServiceRepository $service_gestion
	* @param  App\Repositories\CommentRepository $comment_gestion
	* @return Response
	*/
	public function admin(
		ContactRepository $contact_gestion, 
		ServiceRepository $service_gestion,
		CommentRepository $comment_gestion)
	{	
		$nbrMessages = $contact_gestion->getNumber();
		$nbrPosts = $service_gestion->getNumber();
		$nbrComments = $comment_gestion->getNumber();

		return view('back.index', compact('nbrMessages', 'nbrPosts', 'nbrComments'));
	}

	/**
	 * Show the media panel.
	 *
     * @return Response
	 */
	public function filemanager()
	{
		$url = config('medias.url') . '?langCode=' . config('app.locale');
		
		return view('back.filemanager', compact('url'));

	}

}
