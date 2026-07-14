<?php
namespace App\Actions\Post;

use App\Enums\PostStatus;
use App\Models\Post\Post;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CreatePost
{
    public function __construct(
        #[CurrentUser] protected User $user,
        protected ImageService $service
    ) {}

    public function handle(
        array $attributes, ?UploadedFile $image = null
    ): Post {

        // collect all validated data
        $data = collect($attributes)->only([
            'title', 'subtitle', 'body', 'status',
        ])->toArray();

        $data['status'] = PostStatus::Pending;

        // store image if uploaded
        $imagePath = null;
        if ($image) {
            $imagePath          = $this->service->storeImage($image);
            $data['image_path'] = $imagePath;
        }

        try {
            $post = DB::transaction(function () use ($data, $attributes) {
                // create new post
                $post = $this->user->posts()->create($data);

                // create tags if exists
                if (isset($attributes['tags'])) {
                    $tags = explode(",", $attributes['tags']);

                    foreach ($tags as $tag) {
                        $post->tag($tag);
                    }
                }

                return $post;
            });
        } catch (\Exception $exception) {
            // cleanup orphaned image if DB failed
            if ($imagePath) {
                $this->service->deleteImage($imagePath);
            }

            // controller handles this exception
            throw $exception;
        }

        return $post->load('tags');
    }
}