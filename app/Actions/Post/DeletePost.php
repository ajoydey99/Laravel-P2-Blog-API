<?php
namespace App\Actions\Post;

use App\Models\Post\Post;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Container\Attributes\CurrentUser;

class DeletePost
{
    public function __construct(
        #[CurrentUser] protected User $user,
        protected ImageService $service
    ) {}

    public function handle(Post $post)
    {
        $image = $post->image_path;

        if ($image) {
            $this->service->deleteImage($image);
        }
        $post->delete();
    }
}
