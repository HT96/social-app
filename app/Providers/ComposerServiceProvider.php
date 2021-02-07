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
            if ($userId = Auth::id()) {
                $view->with('incomingFriendRequestsCount', (new UserRelationship())->getIncomingFriendRequests($userId)->count());
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
