<?php
namespace App\Http\Controllers\Api\v1\Posts;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post\Post;
use App\Models\Tag\Tag;
use App\Services\ImageService;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class PostsController extends Controller implements HasMiddleware
{
    use ApiResponses;

    public function __construct(protected ImageService $service)
    {}

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index']),
            new Middleware('can:edit,post', only: ['update', 'destroy']),
        ];
    }

    public function index()
    {
        $posts = Post::with(['user', 'tags'])
            ->where('status', PostStatus::Published)
            ->get();

        return $this->successResponse(
            PostResource::collection($posts),
            "All posts are returned"
        );
    }

    public function store(Request $request)
    {
        try {
            // after validation post create
            $post = Post::create([
                'title'    => $request->title,
                'subtitle' => $request->subtitle,
                'body'     => $request->body,
                'user_id'  => $request->user()->id,
                'views'    => 0,
                'status'   => PostStatus::Pending,
            ]);

            // create tags if exists
            if ($request->has('tags')) {
                $tags = explode(",", $request['tags']);

                foreach ($tags as $tag) {
                    $post->tag($tag);
                }
            }

            // store and save image if uploaded
            if ($request->hasFile('image')) {
                // update image path to database
                $post->image_path = $this->service->storeImage($request->file('image'));
                $post->save();
            }

        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
            );
        }

        return $this->successResponse(
            new PostResource($post->load('tags')),
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

        // count views
        $post->views++;
        $post->save();

        return $this->successResponse(
            new PostResource($post->load('tags')),
        );
    }

    public function update(Request $request, Post $post)
    {
        try {
            $post->update([
                'title'    => $request->title ?? $post->title,
                'subtitle' => $request->subtitle ?? $post->subtitle,
                'body'     => $request->body ?? $post->body,
            ]);

            if ($request->has('tags')) {
                $post->syncTags($request->tags);
            }

            if ($request->hasFile('image')) {
                // validate image
                $request->validate([
                    'image' => 'image|max:2048',
                ]);

                // delete old image
                $oldImage = $post->image_path;
                if ($oldImage) {
                    $this->service->deleteImage($oldImage);
                }

                // update image path to database
                $post->image_path = $this->service->storeImage($request->file('image'));
                $post->save();
            }

        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
            );
        }

        return $this->successResponse(
            new PostResource($post->load('tags')),
            "Post updated successfully",
        );
    }

    public function destroy(Post $post)
    {
        $image = $post->image_path;

        if ($image) {
            $this->service->deleteImage($image);
        }
        $post->delete();

        return $this->successResponse(
            null, "Post deleted succsessfully"
        );
    }

    public function updatePostStatus(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', new Enum(PostStatus::class)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'           => false,
                'message'          => $validator->errors(),
                'allowed_statuses' => array_map(
                    fn($case) => [
                        'name'  => $case->name,
                        'value' => $case->value,
                    ], PostStatus::cases()
                ),
            ], 422);
        }

        $post->status = $request->status;
        $post->save();

        return $this->successResponse(
            new PostResource($post->fresh()),
            'Post status updated'
        );
    }

    public function postsByUser()
    {
        $posts = auth()->user()->posts()->latest()->get();

        if ($posts->isEmpty()) {
            return $this->successResponse(
                null, "no posts created by user",
            );
        }

        return $this->successResponse(
            PostResource::collection($posts),
            "All posts created by user have returned"
        );
    }

    public function getPostsByTag(Tag $tag)
    {
        $posts = $tag->posts()->with(['user', 'tags'])->latest()->get();

        return $this->successResponse(
            PostResource::collection($posts),
            "All posts related to '{$tag->name}' tag returned"
        );
    }

}
