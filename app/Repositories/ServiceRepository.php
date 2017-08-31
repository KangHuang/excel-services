<?php

namespace App\Repositories;

use App\Models\Service,
    App\Models\Comment;

class ServiceRepository extends BaseRepository {

   

    /**
     * The Comment instance.
     *
     * @var App\Models\Comment
     */
    protected $comment;

    /**
     * Create a new ServiceRepository instance.
     *
     * @param  App\Models\Service $service
     * @param  App\Models\Comment $comment
     * @return void
     */
    public function __construct(
    Service $service, Comment $comment) 
    {
        $this->model = $service;
        $this->comment = $comment;
    }

    /**
     * Create or update a service.
     *
     * @param  App\Models\Service $service
     * @param  array  $inputs
     * @param  bool   $provider_id
     * @return App\Models\Service
     */
    private function saveService($service, $inputs, $provider_id)
    {

        $service->title = $inputs['title'];
        $service->description = $inputs['description'];
        $service->filename = $inputs['filename_ori'];
        $service->price = $inputs['price'];
        $service->active = isset($inputs['active']);
        $service->provider_id = $provider_id;
        $service->hid_fin = $inputs['hid_fin'];
        $service->hid_tec = $inputs['hid_tec'];
        $service->save();

        return $service;
    }

    /**
     * Create a query for Service.
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    private function queryActiveWithUserOrderByDate()
    {
        return $this->model
            ->select('id', 'created_at', 'updated_at','filename', 'title', 'price', 'provider_id', 'description')
                        ->whereActive(true)
                        ->with('provider')
                        ->latest();
    }

    /**
     * Get service collection.
     *
     * @param  int  $n
     * @return Illuminate\Support\Collection
     */
    public function indexFront($n)
    {
        $query = $this->queryActiveWithUserOrderByDate();

        return $query->paginate($n);
    }

    /**
     * Get service collection.
     *
     * @param  int  $n
     * @param  int  $id
     * @return Illuminate\Support\Collection
     */
//    public function indexTag($n, $id)
//    {
//        $query = $this->queryActiveWithUserOrderByDate();
//
//        return $query->whereHas('tags', function($q) use($id) {
//                            $q->where('tags.id', $id);
//                        })
//                        ->paginate($n);
//    }

    /**
     * Get search collection.
     *
     * @param  int  $n
     * @param  string  $search
     * @return Illuminate\Support\Collection
     */
    public function search($n, $search)
    {
        $query = $this->queryActiveWithUserOrderByDate();

        return $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%$search%")
                            ->orWhere('filename', 'like', "%$search%")
                            ->orWhere('title', 'like', "%$search%");
                })->paginate($n);
    }

    /**
     * Get service collection.
     *
     * @param  int     $n
     * @param  int     $provider_id
     * @param  string  $orderby
     * @param  string  $direction
     * @return Illuminate\Support\Collection
     */
    public function index($n, $provider_id = null, $orderby = 'created_at', $direction = 'desc')
    {
        $query = $this->model
                ->select('services.id', 'services.created_at', 'title', 'services.seen', 'services.active', 'provider_id', 'price')
                ->join('providers', 'providers.id', '=', 'services.provider_id')
                ->orderBy($orderby, $direction);

  
        $query->where('provider_id', $provider_id);
       

        return $query->paginate($n);
    }

    /**
     * Get service collection.
     *
     * @param  string  $id
     * @return array
     */
    public function show($id)
    {
        $service = $this->model->findOrFail($id);
        
        $comments = $this->comment
                ->whereService_id($service->id)
                ->with('user')
                ->whereHas('user', function($q) {
                    $q->whereValid(true);
                })
                ->get();

        return compact('service', 'comments');
    }

    /**
     * Get service collection.
     *
     * @param  App\Models\Service $service
     * @return array
     */
//    public function edit($service)
//    {
//        $tags = [];
//
//        foreach ($service->tags as $tag) {
//            array_push($tags, $tag->tag);
//        }
//
//        return compact('service', 'tags');
//    }

    /**
     * Get service collection.
     *
     * @param  int  $id
     * @return array
     */
//    public function GetByIdWithTags($id)
//    {
//        return $this->model->with('tags')->findOrFail($id);
//    }

    /**
     * Update a service.
     *
     * @param  array  $inputs
     * @param  App\Models\Service $service
     * @return void
     */
    public function update($inputs, $id)
    {
        $service = $this->getById($id);

        $service->title = $inputs['title'];
        $service->description = $inputs['description'];
        $service->price = $inputs['price'];
        $service->hid_fin = $inputs['hid_fin'];
        $service->hid_tec = $inputs['hid_tec'];
        $service->save();
        
        return $service;
    }

    /**
     * Update "seen" in service.
     *
     * @param  array  $inputs
     * @param  int    $id
     * @return void
     */
    public function updateSeen($inputs, $id)
    {
        $service = $this->getById($id);

        $service->seen = $inputs['seen'] == 'true';

        $service->save();
    }

    /**
     * Update "active" in service.
     *
     * @param  array  $inputs
     * @param  int    $id
     * @return void
     */
    public function updateActive($inputs, $id)
    {
        $service = $this->getById($id);

        $service->active = $inputs['active'] == 'true';


        $service->save();
    }

    /**
     * Create a service.
     *
     * @param  array  $inputs
     * @param  int    $provider_id
     * @return void
     */
    public function store($inputs, $provider_id)
    {
        $service = $this->saveService(new $this->model, $inputs, $provider_id);

    }

    /**
     * Destroy a service.
     *
     * @param  App\Models\Service $service
     * @return void
     */
    public function destroy($service) {
        $service->users()->detach();
        $service->delete();
    }

    /**
     * Get service price.
     *
     * @param  int  $comment_id
     * @return string
     */
    public function getSlug($comment_id)
    {
        return $this->comment->findOrFail($comment_id)->service->price;
    }

    /**
     * Get tag name by id.
     *
     * @param  int  $tag_id
     * @return string
     */
    public function getTagById($tag_id)
    {
        return $this->tag->findOrFail($tag_id)->tag;
    }

}
