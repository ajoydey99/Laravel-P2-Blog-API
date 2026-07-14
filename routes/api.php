<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\v1\Posts\PostsController as V1PostController;
use App\Http\Controllers\Api\v2\Posts\PostsController as V2PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// auth routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// API VERSION 1
Route::prefix('v1')->name('v1.')->group(function () {
    // posts resource routes
    Route::apiResource('posts', V1PostController::class);

    // filter routes
    Route::get('user-posts', [V1PostController::class, 'postsByUser'])->name('auth.posts');
    Route::get('tags/{tag}/posts', [V1PostController::class, 'getPostsByTag'])->name('tag.posts');

    // admin routes
    Route::patch('posts/{post}/status', [V1PostController::class, 'updatePostStatus'])->middleware('role:admin');
});

// API VERSION 2
Route::prefix('v2')->name('v2.')->group(function () {
    // posts resource routes
    Route::apiResource('posts', V2PostController::class);

    // filter routes
    Route::get('user-posts', [V2PostController::class, 'postsByUser'])->name('auth.posts');
    Route::get('tags/{tag}/posts', [V2PostController::class, 'getPostsByTag'])->name('tag.posts');
    Route::get('users/{user}/posts', [V2PostController::class, 'getPostsByUserID'])->name('user.posts');

    // admin routes
    Route::patch('posts/{post}/status', [V2PostController::class, 'updatePostStatus'])->middleware('role:admin');
});

// http://apidoc.test/
