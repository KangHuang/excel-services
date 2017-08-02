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
     * @param  App\Models\Service $post
     * @param  App\Models\Comment $comment
     * @return void
     */
    public function __construct(
    Service $post, Comment $comment) 
    {
        $this->model = $post;
        $this->comment = $comment;
    }

    /**
     * Create or update a post.
     *
     * @param  App\Models\Service $post
     * @param  array  $inputs
     * @param  bool   $provider_id
     * @return App\Models\Service
     */
    private function saveService($post, $inputs, $provider_id)
    {

        $post->title = $inputs['title'];
        $post->description = $inputs['description'];
        $post->filename = $inputs['filename_ori'];
        $post->price = $inputs['price'];
        $post->active = isset($inputs['active']);
        $post->provider_id = $provider_id;
        $post->hid_fin = $inputs['hid_fin'];
        $post->hid_tec = $inputs['hid_tec'];
        $post->save();

        return $post;
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
     * Get post collection.
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
     * Get post collection.
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
     * Get post collection.
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
     * Get post collection.
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
     * Get post collection.
     *
     * @param  App\Models\Service $post
     * @return array
     */
//    public function edit($post)
//    {
//        $tags = [];
//
//        foreach ($post->tags as $tag) {
//            array_push($tags, $tag->tag);
//        }
//
//        return compact('post', 'tags');
//    }

    /**
     * Get post collection.
     *
     * @param  int  $id
     * @return array
     */
//    public function GetByIdWithTags($id)
//    {
//        return $this->model->with('tags')->findOrFail($id);
//    }

    /**
     * Update a post.
     *
     * @param  array  $inputs
     * @param  App\Models\Service $post
     * @return void
     */
    public function update($inputs, $post)
    {
        $post = $this->saveService($post, $inputs);

        // Tag gestion
        $tags_id = [];
        if (array_key_exists('tags', $inputs) && $inputs['tags'] != '') {

            $tags = explode(',', $inputs['tags']);

            foreach ($tags as $tag) {
                $tag_ref = $this->tag->whereTag($tag)->first();
                if (is_null($tag_ref)) {
                    $tag_ref = new $this->tag();
                    $tag_ref->tag = $tag;
                    $tag_ref->save();
                }
                array_push($tags_id, $tag_ref->id);
            }
        }

        $post->tags()->sync($tags_id);
    }

    /**
     * Update "seen" in post.
     *
     * @param  array  $inputs
     * @param  int    $id
     * @return void
     */
    public function updateSeen($inputs, $id)
    {
        $post = $this->getById($id);

        $post->seen = $inputs['seen'] == 'true';

        $post->save();
    }

    /**
     * Update "active" in post.
     *
     * @param  array  $inputs
     * @param  int    $id
     * @return void
     */
    public function updateActive($inputs, $id)
    {
        $post = $this->getById($id);

        $post->active = $inputs['active'] == 'true';


        $post->save();
    }

    /**
     * Create a post.
     *
     * @param  array  $inputs
     * @param  int    $provider_id
     * @return void
     */
    public function store($inputs, $provider_id)
    {
        $post = $this->saveService(new $this->model, $inputs, $provider_id);

    }

    /**
     * Destroy a post.
     *
     * @param  App\Models\Service $post
     * @return void
     */
    public function destroy($post) {
        $post->users()->detach();
        $post->delete();
    }

    /**
     * Get post price.
     *
     * @param  int  $comment_id
     * @return string
     */
    public function getSlug($comment_id)
    {
        return $this->comment->findOrFail($comment_id)->post->price;
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
