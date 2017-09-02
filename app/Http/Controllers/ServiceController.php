<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Http\Requests\ServiceRequest,
    App\Http\Requests\ServiceUpdateRequest;
use App\Http\Requests\SearchRequest;
use App\Repositories\ServiceRepository,
    App\Repositories\UserRepository,
    App\Repositories\ProviderRepository,
    App\Repositories\RelationRepository;
use Illuminate\Support\Facades\File;

class ServiceController extends Controller {

    /**
     * The ServiceRepository instance.
     *
     * @var App\Repositories\ServiceRepository
     */
    protected $service_handler;

    /**
     * The UserRepository instance.
     *
     * @var App\Repositories\UserRepository
     */
    protected $user_handler;

    /**
     * The UserRepository instance.
     *
     * @var App\Repositories\ProviderRepository
     */
    protected $provider_handler;

    /**
     * The UserRepository instance.
     *
     * @var App\Repositories\RelationRepository
     */
    protected $relation_handler;

    /**
     * The pagination number.
     *
     * @var int
     */
    protected $nbrPages;

    /**
     * Create a new ServiceController instance.
     *
     * @param  App\Repositories\ServiceRepository $service_handler
     * @param  App\Repositories\UserRepository $user_handler
     * ...
     * @return void
     */
    public function __construct(
    ServiceRepository $service_handler, UserRepository $user_handler, ProviderRepository $provider_handler, RelationRepository $relation_handler) {
        $this->user_handler = $user_handler;
        $this->service_handler = $service_handler;
        $this->provider_handler = $provider_handler;
        $this->relation_handler = $relation_handler;
        $this->nbrPages = 3;
    }

    /**
     * Present a list of the resource.
     *
     * @return Response
     */
    public function indexFront() {
        $posts = $this->service_handler->indexFront($this->nbrPages);
        $links = $posts->render();

        return view('front.service.index', compact('posts', 'links'));
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Illuminate\Http\Request $request
     * @return Response
     */
    public function indexOrder(Request $request) {

        $statut = $this->user_handler->getStatut();
        $services = auth()->guard('providers')->user()->services()->paginate(10);


        $posts = $services;

        return view('back.service.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('back.service.create')->with(compact('url'));
    }

    /**
     * Show the page for configuring user's accessibility
     * @param $id
     * @return Response
     */
    public function config($service_id) {

        $users = $this->user_handler->index(10, 'manager');
        $usersPermit = $this->service_handler->getById($service_id)->users()->paginate(10);
        $post = $this->service_handler->getById($service_id);

        return view('back.service.config', compact('service_id', 'users', 'usersPermit', 'post'));
    }

    /**
     * store a new service
     *
     * @param  App\Http\Requests\ServiceRequest $request
     * @return Response
     */
    public function store(ServiceRequest $request) {
        $name = $request->file('filename')->getClientOriginalName();
        $unique_name = md5($name . time());
        $request->file('filename')->move('excel', $unique_name);
        $request->merge(['filename_ori' => $unique_name]);
        $this->service_handler->store($request->all(), auth()->guard('providers')->user()->id);

        return redirect('service/order')->with('ok', trans('back/service.stored'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  service id
     * @param  int  $id
     * @return Response
     */
    public function edit($service_id) {

        $service = $this->service_handler->getById($service_id);

        return view('back.service.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PostUpdateRequest $request
     * @param  int  $id
     * @return Response
     */
    public function update(
    ServiceUpdateRequest $request, $service_id) {
        $this->service_handler->update($request->all(), $service_id);

        return redirect('service/order')->with('ok', trans('back/service.updated'));
    }

    /**
     * Update "active" for the specified resource in storage.
     *
     * @param  Illuminate\Http\Request $request
     * @param  int  $id
     * @return Response
     */
    public function updateActive(
    Request $request, $id) {
        $post = $this->service_handler->getById($id);

        $this->service_handler->updateActive($request->all(), $id);

        return response()->json();
    }

    /**
     * Remove the specified service
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($service_id) {
        $post = $this->service_handler->getById($service_id);

        File::delete('excel/' . $post->filename);

        $this->service_handler->destroy($post);

        return redirect('service/order')->with('ok', trans('back/service.destroyed'));
    }

    /**
     * build relationship between service and user
     *
     * @param  App\Http\Requests\SearchRequest $request
     * @return Response
     */
    public function relation(Request $request, $user_id) {
        $service_id = $request->input('service_id');
        if ($request->input('active') == 'true') {
            $this->service_handler->getById($service_id)->users()->attach($user_id);
        } else {
            $this->service_handler->getById($service_id)->users()->detach($user_id);
        }
        return response()->json();
    }

    /**
     * establish a payment
     *
     * @param  App\Http\Requests\SearchRequest $request $service_id
     * @return Response
     */
    public function makePayment(Request $request, $service_id) {
        $service = $this->service_handler->getById($service_id);
        return view('front.service.payment', compact('service'));
    }

}
