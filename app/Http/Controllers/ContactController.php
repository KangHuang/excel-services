<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Repositories\ContactRepository;

class ContactController extends Controller {

	/**
	 * Create a new ContactController instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('director', ['except' => ['create', 'store']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param  ContactRepository $contact_gestion
	 * @return Response
	 */
	public function index(
		ContactRepository $contact_gestion)
	{
		$messages = $contact_gestion->index();

		return view('back.messages.index', compact('messages'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('front.contact');
	}

	/**
	 * Store a contact message for visitors
	 *
	 * @param  App\Repositories\ContactRepository $contact_gestion
	 * @param  ContactRequest $request
	 * @return Response
	 */
	public function store(
		ContactRepository $contact_gestion,
		ContactRequest $request)
	{
		$contact_gestion->store($request->all());

		return redirect('/')->with('ok', trans('front/contact.ok'));
	}

	/**
	 * delete a contact message
	 *
	 * @param  App\Repositories\ContactRepository $contact_gestion
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(
		ContactRepository $contact_gestion, 
		$id)
	{
		$contact_gestion->destroy($id);
		
		return redirect('contact');
	}

}
