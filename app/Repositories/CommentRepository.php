<?php namespace App\Repositories;

use App\Models\Comment;

class CommentRepository extends BaseRepository {

	/**
	 * Create a new CommentRepository instance.
	 *
	 * @param  App\Models\Comment $comment
	 * @return void
	 */
	public function __construct(Comment $comment)
	{
		$this->model = $comment;
	}

	/**
	 * Get comments collection.
	 *
	 * @param  int  $n
	 * @return Illuminate\Support\Collection
	 */
	public function index($n)
	{
		return $this->model
		->with('service', 'user')
		->oldest('seen')
		->latest()
		->paginate($n);
	}

	/**
	 * Store a comment.
	 *
	 * @param  array $inputs
	 * @param  int   $user_id
	 * @return void
	 */
 	public function store($inputs, $user_id)
	{
		$comment = new $this->model;	

		$comment->content = $inputs['content'];
		$comment->service_id = $inputs['service_id'];
		$comment->user_id = $user_id;

		$comment->save();
	}

}