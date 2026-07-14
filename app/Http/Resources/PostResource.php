<?php
namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"        => $this->id,
            "title"     => $this->title,
            "subtitle"  => $this->subtitle,
            "body"      => $this->body,
            "status"    => $this->status->label(),
            "views"     => $this->views,
            "tags"      => $this->loadTags(),
            "image_url" => $this->image_url,
            "user"      => new UserResource($this->whenLoaded('user')),
        ];
    }

    public function loadTags()
    {
        return $this->whenLoaded('tags', fn() => $this->tags->pluck('name'));
    }
}