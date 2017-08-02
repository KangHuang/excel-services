<?php namespace App\Repositories;

use App\Models\Relation;

class RelationRepository extends BaseRepository {

	/**
	 * Create a new RelationRepository instance.
	 *
	 * @param  App\Models\Relation $relation
	 * @return void
	 */
	public function __construct(Relation $relation)
	{
		$this->model = $relation;
	}
        
        /**
	 * Get a new RelationRepository instance.
	 *
	 * @param  App\Models\Relation $relation
	 * @return collection
	 */
	public function get($user_id, $service_id)
	{
            
	}
        
	/**
	 * Store a relation.
	 *
	 * @param  int $service_id
	 * @param  int   $user_id
	 * @return void
	 */
 	public function store($service_id, $user_id)
	{
		$relation = new $this->model;	

		$relation->service_id = $service_id;
		$relation->user_id = $user_id;

		$relation->save();
	}
        
        /**
     * Destroy a post.
     *
     * @param  App\Models\Relation $relation
     * @return void
     */
         public function destroy($relation) {
                $relation->delete();
         }
        

}