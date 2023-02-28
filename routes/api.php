<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/401', [AuthController::class, 'unauthorized'])->name('unauthorized');

Route::get('/ping', function() {
  return 'pong';
});

//Register routes
Route::post('/auth/register', [AuthController::class, 'registerAction'])->name('registerAction');
Route::get('/user/register', [UserController::class, 'register'])->name('register');

//Login routes
Route::post('/auth/login', [AuthController::class, 'loginAction'])->name('loginAction');
Route::get('/user/login', [UserController::class, 'login'])->name('login');

//Logout route
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

//Refresh token route
Route::post('/auth/refresh', [AuthController::class, 'refreshToken'])->name('refreshToken');

//User profile route
Route::get('/user/profile', [UserController::class, 'myProfile'])->name('myProfile');

//User profile route (guest)
Route::get('/user/profile/{user}', [UserController::class, 'userProfile'])->name('userProfile');

//User edit profile route
Route::put('/user/edit', [UserController::class, 'editProfileAction'])->name('editProfileAction');
Route::post('/user/{user}/edit/picture', [UserController::class, 'updateProfilePictureAction'])->name('updateProfilePictureAction');
Route::get('/user/edit', [UserController::class, 'edit'])->name('edit');

//Post create and edit routes
Route::post('/post/create', [PostController::class, 'createPostAction'])->name('createPostAction');
Route::put('/post/{post}', [PostController::class, 'editPostAction'])->name('editPostAction');
Route::post('/post/{post}/picture', [PostController::class, 'updatePostPictureAction'])->name('updatePostPictureAction');

//Post show routes
Route::get('/post/show', [PostController::class, 'showAllPosts'])->name('showAllPosts');
Route::get('/post/show/{post}', [PostController::class, 'showOnePost'])->name('showOnePost');

//Post delete route
Route::delete('post/{post}', [PostController::class, 'delete'])->name('delete');

// Create review route
Route::post('/post/{post}/review', [ReviewController::class, 'createReview'])->name('createReview');

// Show review route
Route::get('/post/{post}/review', [ReviewController::class, 'showReviews'])->name('showReviews');

// Delete review route
Route::delete('/post/{post}/review/{review}', [ReviewController::class, 'deleteReview'])->name('deleteReview');