<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use App\Repositories\CommentRepository;
use App\Repositories\UserRepository;

class CommentController extends Controller {

     /*
     * @var App\Repositories\CommentRepository
     */
    protected $comment_handler;

    /**
     * Constructor with dependency injection.
     *
     * @param  App\Repositories\CommentRepository $comment_handler
     * @return void
     */
    public function __construct(
    CommentRepository $comment_handler) {
        $this->comment_handler = $comment_handler;

        $this->middleware('director', ['except' => ['store']]);
        $this->middleware('usepermit', ['only' => 'store']);
    }

    /**
     * shows the comments
     *
     * @return Response
     */
    public function index() {
        $comments = $this->comment_handler->index(4);
        $links = $comments->render();

        return view('back.comments.index', compact('comments', 'links'));
    }

    /**
     * save a comment.
     *
     * @param  App\requests\CommentRequest $request
     * @return Response
     */
    public function store(
    CommentRequest $request) {
        
        $user_id = auth()->guard('users')->id();

        $this->comment_handler->store($request->all(), $user_id);


        return redirect('services')->with('ok', 'comment success');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @param  int  $id
     * @return Response
     */
    public function destroy(
    Request $request, $id) {
        $this->comment_handler->destroy($id);

        if ($request->ajax()) {
            return response()->json(['id' => $id]);
        }

        return redirect('comment');
    }

}
