<?php

namespace App\Http\Controllers;

use App\Http\Requests\FriendRequest;
use App\Models\UserRelationship;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FriendsController extends Controller
{
    /**
     * Display the friends view.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('friends.index', [
            'relationshipStatuses' => UserRelationship::STATUSES
        ]);
    }

    /**
     * Handle add to friend request.
     *
     * @param FriendRequest  $request
     * @return JsonResponse
     */
    public function add(FriendRequest $request)
    {
        $senderId = Auth::id();
        $receiverId = $request->get('user_id');
        if ($receiverId == $senderId || !$request->wantsJson()) {
            abort(404);
        }

        $relationship = new UserRelationship();
        if ($relationship->activeRequestExists($senderId, $receiverId)) {
            return response()->json(['message' => 'You have already sent a friend request.'], 400);
        }

        $relationship->addFriendRequest($senderId, $receiverId);

        return response()->json(['message' => 'Friend request sent successfully.']);
    }

    /**
     * Handle add to friend request.
     *
     * @param FriendRequest  $request
     * @return JsonResponse
     */
    public function delete(FriendRequest $request)
    {
        $senderId = Auth::id();
        $receiverId = $request->get('user_id');
        if ($receiverId == $senderId || !$request->wantsJson()) {
            abort(404);
        }

        $relationship = new UserRelationship();
        if ($relationship->deleteFriendRequest($senderId, $receiverId)) {
            return response()->json(['message' => 'Friend deleted successfully.']);
        }

        return response()->json(['message' => 'Friend not fount.'], 404);
    }

    /**
     * Handle approve add to friend request.
     *
     * @param FriendRequest  $request
     * @return JsonResponse
     */
    public function approve(FriendRequest $request)
    {
        $receiverId = Auth::id();
        $senderId = $request->get('user_id');
        if ($receiverId == $senderId || !$request->wantsJson()) {
            abort(404);
        }

        $relationship = new UserRelationship();
        if ($relationship->approveFriendRequest($senderId, $receiverId)) {
            return response()->json(['message' => 'Friend request approved successfully.']);
        }

        return response()->json(['message' => 'Friend request not fount.'], 404);
    }

    /**
     * Handle reject add to friend request.
     *
     * @param FriendRequest  $request
     * @return JsonResponse
     */
    public function reject(FriendRequest $request)
    {
        $receiverId = Auth::id();
        $senderId = $request->get('user_id');
        if ($receiverId == $senderId || !$request->wantsJson()) {
            abort(404);
        }

        $relationship = new UserRelationship();
        if ($relationship->rejectFriendRequest($senderId, $receiverId)) {
            return response()->json(['message' => 'Friend request rejected successfully.']);
        }

        return response()->json(['message' => 'Friend request not fount.'], 404);
    }
}
