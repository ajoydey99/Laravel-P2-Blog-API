<?php
namespace App\Policies;

use App\Models\Post\Post;
use App\Models\User;

class PostPolicy
{
    public function edit(User $user, Post $post)
    {
        return $post->user->is($user);
    }
}