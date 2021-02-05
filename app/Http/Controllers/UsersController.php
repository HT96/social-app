<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        return view('users.index');
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

        $query = User::query()
            ->select(['id', 'name', 'surname'])
            ->where('id', '!=', Auth::id());

        if ($search = $request->get('search')) {
            $search = explode(' ', trim($search));
            $query->where(function($subQuery) use ($search) {
                foreach ($search as $word) {
                    if ($word) {
                        $subQuery->orWhere('name', 'like', "%$word%")
                            ->orWhere('surname', 'like', "%$word%");
                    }
                }
            });
        }

        return response()->json($query->get());
    }
}
