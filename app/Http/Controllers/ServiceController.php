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
    protected $service_gestion;

    /**
     * The UserRepository instance.
     *
     * @var App\Repositories\UserRepository
     */
    protected $user_gestion;

    /**
     * The UserRepository instance.
     *
     * @var App\Repositories\ProviderRepository
     */
    protected $provider_gestion;

    /**
     * The UserRepository instance.
     *
     * @var App\Repositories\RelationRepository
     */
    protected $relation_gestion;

    /**
     * The pagination number.
     *
     * @var int
     */
    protected $nbrPages;

    /**
     * Create a new ServiceController instance.
     *
     * @param  App\Repositories\ServiceRepository $service_gestion
     * @param  App\Repositories\UserRepository $user_gestion
     * @return void
     */
    public function __construct(
    ServiceRepository $service_gestion, UserRepository $user_gestion, ProviderRepository $provider_gestion, RelationRepository $relation_gestion) {
        $this->user_gestion = $user_gestion;
        $this->service_gestion = $service_gestion;
        $this->provider_gestion = $provider_gestion;
        $this->relation_gestion = $relation_gestion;
        $this->nbrPages = 3;

        $this->middleware('ajax', ['only' => ['updateSeen', 'updateActive']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function indexFront() {
        $posts = $this->service_gestion->indexFront($this->nbrPages);
        $links = $posts->render();

        return view('front.service.index', compact('posts', 'links'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Redirection
     */
    public function index() {
        return redirect(route('service.order', [
            'name' => 'posts.created_at',
            'sens' => 'asc'
        ]));
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Illuminate\Http\Request $request
     * @return Response
     */
    public function indexOrder(Request $request) {

        $request->name = 'services.created_at';
        $request->sens = 'desc';
        $statut = $this->user_gestion->getStatut();
        $posts = auth()->guard('providers')->user()->services()->paginate(10);

        $links = $posts->appends([
            'name' => $request->name,
            'sens' => $request->sens
        ]);

        if ($request->ajax()) {
            return response()->json([
                        'view' => view('back.service.table', compact('statut', 'posts'))->render(),
                        'links' => e($links->setPath('order')->render())
            ]);
        }

        $links->setPath('')->render();

        $order = (object) [
                    'name' => $request->name,
                    'sens' => 'sort-' . $request->sens
        ];

        return view('back.service.index', compact('posts', 'links', 'order'));
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

        $users = $this->user_gestion->index(10, 'manager');
        $usersPermit = $this->service_gestion->getById($service_id)->users()->paginate(10);
        $post = $this->service_gestion->getById($service_id);
        return view('back.service.config', compact('service_id', 'users', 'usersPermit', 'post'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PostRequest $request
     * @return Response
     */
    public function store(ServiceRequest $request) {
        $name = $request->file('filename')->getClientOriginalName();

        $unique_name = md5($name . time());

        $request->file('filename')->move('excel', $unique_name);

        $request->merge(['filename_ori' => $unique_name]);

        $this->service_gestion->store($request->all(), auth()->guard('providers')->user()->id);

        return redirect('service/order')->with('ok', trans('back/service.stored'));
    }

    /**
     * Display the specified resource.
     *
     * @param  Illuminate\Contracts\Auth\Guard $auth	 
     * @param  string $slug
     * @return Response
     */
    public function show(
    Guard $auth, $slug) {
        $user = $auth->user();

        return view('front.service.show', array_merge($this->service_gestion->show($slug), compact('user')));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  service id
     * @param  int  $id
     * @return Response
     */
    public function edit($service_id) {

        $service = $this->service_gestion->getById($service_id);

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
        $this->service_gestion->update($request->all(), $service_id);

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
        $post = $this->service_gestion->getById($id);

        $this->service_gestion->updateActive($request->all(), $id);

        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($service_id) {
        $post = $this->service_gestion->getById($service_id);

        File::delete('excel/' . $post->filename);

        $this->service_gestion->destroy($post);

        return redirect('service/order')->with('ok', trans('back/service.destroyed'));
    }

    /**
     * Find search in service
     *
     * @param  App\Http\Requests\SearchRequest $request
     * @return Response
     */
    public function search(SearchRequest $request) {
        $search = $request->input('search');
        $posts = $this->service_gestion->search($this->nbrPages, $search);
        $links = $posts->appends(compact('search'))->render();
        $info = trans('front/service.info-search') . '<strong>' . $search . '</strong>';

        return view('front.service.index', compact('posts', 'links', 'info'));
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
            $this->service_gestion->getById($service_id)->users()->attach($user_id);
        } else {
            $this->service_gestion->getById($service_id)->users()->detach($user_id);
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
        $service = $this->service_gestion->getById($service_id);
        return view('front.service.payment', compact('service'));
    }

}
