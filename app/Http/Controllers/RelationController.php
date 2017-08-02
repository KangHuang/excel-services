<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\RelationRepository;
use App\Repositories\UserRepository;

class RelationController extends Controller {

    /**
     * The RelationRepository instance.
     *
     * @var App\Repositories\RelationRepository
     */
    protected $relation_gestion;

    /**
     * Create a new RelationController instance.
     *
     * @param  App\Repositories\RelationRepository $relation_gestion
     * @return void
     */
    public function __construct(
    RelationRepository $relation_gestion) {
        $this->relation_gestion = $relation_gestion;

        $this->middleware('admin', ['except' => ['store', 'edit', 'update', 'destroy']]);
        $this->middleware('auth', ['only' => ['store', 'update', 'destroy']]);
        $this->middleware('ajax', ['only' => ['updateSeen', 'update', 'valid']]);
    }

    /**
     * update (add or delete) relations in storage.
     *
     * @param  App\requests\RelationRequest $request
     * @return Response
     */
    public function update(Request $request, $user_id) {

        if ($request->input('active')==true) {
            $this->relation_gestion->store($request->input('service_id'), $user_id);
        } else{
            $relation->$this->relation_gestion->
            $this->relation_gestion->destroy($relation);
        }

        return redirect()->back()->with('warning', trans('front/blog.warning'));
    }

}
