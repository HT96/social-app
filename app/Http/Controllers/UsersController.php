<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRelationship;
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
     * Get the users list.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList(Request $request)
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
        $users = User::getWithRelationship(Auth::id(), $keywords)->get();

        return response()->json($users);
    }
}
