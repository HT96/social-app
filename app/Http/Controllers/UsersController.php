<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRelationship;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    /**
     * Display the users view.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('users.index', [
            'relationshipStatuses' => UserRelationship::STATUSES
        ]);
    }

    /**
     * Display user profile.
     *
     * @param  int $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($id)
    {
        $currentId = Auth::id();
        if ($id == $currentId) {
            $user = Auth::user();
            $user->isCurrent = true;
        } else {
            $user = User::getWithRelationship($currentId)
                ->where('users.id', '=', $id)
                ->first();
            if ( !$user) {
                abort(404);
            }
            $user->isCurrent = false;
        }

        return view('users.show', [
            'user' => $user,
            'relationshipStatuses' => UserRelationship::STATUSES
        ]);
    }

    /**
     * Get the users list.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        if ( !$request->wantsJson()) {
            abort(404);
        }

        $keywords = [];
        if ($search = $request->get('search', '')) {
            foreach (explode(' ', $search) as $word) {
                if ($word) {
                    $keywords[] = $word;
                }
            }
        }
        $users = User::getWithRelationship(Auth::id(), $keywords);
        if ($request->get('only_friends')) {
            $users->where(function(Builder $query) {
                $query->where('send_rel.status', '=', UserRelationship::STATUSES['approved'])
                    ->orWhere('receive_rel.status', '=', UserRelationship::STATUSES['approved']);
            });
        } elseif ($request->get('only_incoming_requests')) {
            $users->where('send_rel.status', '=', UserRelationship::STATUSES['pending']);
        }

        // TODO add limitation
        return response()->json($users->get());
    }
}
