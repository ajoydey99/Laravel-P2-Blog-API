<?php
namespace App\Actions\Post;

use App\Enums\PostStatus;
use App\Models\Post\Post;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdatePost
{
    public function __construct(
        #[CurrentUser] protected User $user,
        protected ImageService $service
    ) {}

    public function handle(
        Post $post,
        array $attributes,
        ?UploadedFile $image = null
    ): Post {

        // collect all validated data
        $data = collect($attributes)->only([
            'title', 'subtitle', 'body',
        ])->toArray();

        // handle image if uploaded
        $imagePath    = null;
        $oldImagePath = $post->image_path;

        if ($image) {
            // store new image
            $imagePath          = $this->service->storeImage($image);
            $data['image_path'] = $imagePath;
        }

        try {
            DB::transaction(function () use ($post, $data, $attributes) {
                // reset post status
                $data['status'] = PostStatus::Pending;
                // update post
                $post->update($data);

                // update tags
                if (isset($attributes['tags'])) {
                    $post->syncTags($attributes['tags']);
                }
            });
        } catch (\Exception $exception) {
            // cleanup newly uploaded image if DB failed
            if ($imagePath) {
                $this->service->deleteImage($imagePath);
            }

            throw $exception;
        }

        // delete old image only after DB succeeded
        if ($imagePath && $oldImagePath) {
            $this->service->deleteImage($oldImagePath);
        }

        return $post->load('tags');
    }
}