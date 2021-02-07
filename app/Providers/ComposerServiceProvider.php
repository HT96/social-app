<?php

namespace App\Providers;

use App\Models\UserRelationship;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        view()->composer('*', function(View $view) {
            $authUser = Auth::user();
            if ($authUser) {
                $view->with('authUser', $authUser)
                    ->with('incomingFriendRequestsCount', (new UserRelationship())->getIncomingFriendRequests($authUser->id)->count());
            }
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
