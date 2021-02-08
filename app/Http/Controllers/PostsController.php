<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\UserRelationship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    /**
     * Display the posts view.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('posts.index');
    }

    /**
     * Create new post.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        if ( !$request->wantsJson()) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'text' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $post = new Post();
        $post->user_id = Auth::id();
        $post->title = $request->get('title');
        $post->public = $request->get('public')? 1: 0;
        $post->text = $request->get('text');
        $post->save();

        return response()->json(['message' => 'Post created successfully.'], 201);
    }

    /**
     * Get the posts list.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        if ( !$request->wantsJson()) {
            abort(404);
        }

        $currentUserId = Auth::id();
        $userId = $request->get('user_id');
        if ($userId) {
            // if it is'n the current user or his friend, get only public posts
            $onlyPublic = !($userId == $currentUserId || (new UserRelationship())->areFriends($userId, $currentUserId));
            $posts = Post::getByUserId($userId, $onlyPublic)->get();
        } else {
            // get all posts of the current user and his friends in the feed
            $friendIds = (new UserRelationship())->getFriendIds($currentUserId);
            $friendIds[] = $currentUserId;
            $posts = Post::getByUserIds($friendIds)->get();
        }

        // TODO add limitation
        return response()->json($posts);
    }
}
