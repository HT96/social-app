<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['web', 'guest'])->group(function() {
    Route::get('/register', [AuthController::class, 'registerPage'])->name('register');

    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'loginPage'])->name('login');

    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['web', 'auth'])->group(function() {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/users', [UsersController::class, 'index'])->name('users');

    Route::get('/users/show/{id}', [UsersController::class, 'show'])->name('users/show/*');

    Route::get('/users/list', [UsersController::class, 'list'])->name('users/list');

    Route::get('/friends', [FriendsController::class, 'index'])->name('friends');

    Route::post('/friends/add', [FriendsController::class, 'add'])->name('friends/add');

    Route::post('/friends/delete', [FriendsController::class, 'delete'])->name('friends/delete');

    Route::post('/friends/approve', [FriendsController::class, 'approve'])->name('friends/approve');

    Route::post('/friends/reject', [FriendsController::class, 'reject'])->name('friends/reject');

    Route::get('/', [PostsController::class, 'index'])->name('/');

    Route::post('/posts/add', [PostsController::class, 'add'])->name('posts/add');

    Route::get('/posts/list', [PostsController::class, 'list'])->name('posts/list');
});
