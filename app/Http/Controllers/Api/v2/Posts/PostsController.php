<?php
namespace App\Http\Controllers\Api\v2\Posts;

use App\Actions\Post\CreatePost;
use App\Actions\Post\DeletePost;
use App\Actions\Post\UpdatePost;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\IndexQueryRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\UpdatePostStatusRequest;
use App\Http\Resources\PostResource;
use App\Models\Post\Post;
use App\Models\Tag\Tag;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class PostsController extends Controller implements HasMiddleware
{
    use ApiResponses;

    public function __construct(
        protected CreatePost $createAction,
        protected UpdatePost $updateAction,
        protected DeletePost $deleteAction

    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index']),
            new Middleware('can:edit,post', only: ['update', 'destroy']),
        ];
    }

    public function index(IndexQueryRequest $request)
    {
        $posts = Post::published($request->validated());

        return $this->successResponse(
            PostResource::collection($posts),
            "All posts are returned"
        );
    }

    public function store(CreatePostRequest $request)
    {
        try {
            // send data to actions
            $post = $this->createAction->handle(
                $request->safe()->all(),
                $request->file('image')
            );

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $this->successResponse(
            new PostResource($post),
            "Post created successfully", 201
        );
    }

    public function show(Post $post)
    {
        // authorization using policy
        $permission = Gate::inspect('edit', $post);
        if (! $permission->allowed()) {
            return $this->permissionResponse(
                "You are not authorized to view this post."
            );
        }

        // check if post is rejected
        if ($post->status === PostStatus::Rejected) {
            return $this->errorResponse(
                null, "This post is rejected by the admin, please update or delete the post");
        }

        // count views
        $post->views++;
        $post->save();

        return $this->successResponse(
            new PostResource($post->load('tags')),
        );
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        try {
            // send data to actions
            $post = $this->updateAction->handle(
                $post, $request->safe()->all(),
                $request->file('image')
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
            );
        }

        return $this->successResponse(
            new PostResource($post),
            "Post updated successfully",
        );
    }

    public function destroy(Post $post)
    {
        $this->deleteAction->handle($post);

        return $this->successResponse(
            null, "Post deleted successfully"
        );
    }

    // filter functions
    public function postsByUser()
    {
        $posts = auth()->user()->posts()->latest()->get();

        if ($posts->isEmpty()) {
            return $this->successResponse(
                null, "no posts created by user",
            );
        }

        return $this->successResponse(
            PostResource::collection($posts->load('tags')),
            "All posts created by user have returned"
        );
    }

    public function getPostsByTag(Tag $tag)
    {
        $posts = $tag->posts()->with(['user', 'tags'])->latest()->get();

        if ($posts->isEmpty()) {
            return $this->successResponse(
                null,
                "No posts found for '{$tag->name}' tag"
            );
        }

        return $this->successResponse(
            PostResource::collection($posts),
            "All posts related to '{$tag->name}' tag returned"
        );
    }

    public function getPostsByUserID(User $user)
    {
        $posts = $user->posts()->latest()->get();

        return $this->successResponse(
            PostResource::collection($posts->load('tags')),
            "All posts related to '{$user->name}' returned"
        );
    }

    // admin actions
    public function updatePostStatus(UpdatePostStatusRequest $request, Post $post)
    {
        $post->update(['status' => $request->status]);

        return $this->successResponse(
            new PostResource($post->fresh()),
            'Post status updated'
        );
    }

}
