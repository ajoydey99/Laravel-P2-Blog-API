<?php
namespace App\Models\Post;

use App\Enums\PostStatus;
use App\Models\Tag\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    // Model Attributes
    protected $fillable = [
        'title',
        'subtitle',
        'body',
        'image_path',
        'user_id',
        'status',
        'views',
    ];

    // get enums for status update
    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
        ];
    }

    // get image path attribute in response
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset(Storage::url($this->image_path)) : null;
    }

    // eloquent relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // tag attach and tag sync
    public function tag(string $name)
    {
        $name = $this->sanitizeTag($name);
        $tag  = Tag::firstOrCreate(['name' => $name]);

        $this->tags()->attach($tag);
    }

    public function syncTags(string $tags)
    {
        $tags = collect(explode(",", $tags))
            ->filter()
            ->map(fn($name) => Tag::firstOrCreate(['name' => $this->sanitizeTag($name)]))
            ->pluck('id');

        $this->tags()->sync($tags);
    }

    public function sanitizeTag(string $name)
    {
        return ucfirst(strtolower(trim($name)));
    }

    // query param functions
    public function scopeSearch(Builder $query, ?string $search = null)
    {
        return $query->when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('subtitle', 'LIKE', "%{$search}%")
                    ->orWhere('body', 'LIKE', "%{$search}%");
            });
        });
    }

    public function scopeSearchTags(Builder $query, ?array $tags = null)
    {
        return $query->when($tags, function ($q) use ($tags) {
            $q->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('name', $tags);
            })->distinct();
        });
    }

    // sort functions
    public function scopeSortPost(Builder $query, ?string $sort = null)
    {
        return match ($sort) {
            'oldest' => $query->oldest(),
            'title'  => $query->orderBy('title'),
            'views'  => $query->orderByDesc('views'),
            default  => $query->latest()
        };
    }

    public function scopePublished(Builder $query, array $filters = [])
    {
        return $query->with(['user', 'tags'])
            ->where('status', PostStatus::Published)
            ->search($filters['search'] ?? null)
            ->searchTags($filters['tags'] ?? null)
            ->sortPost($filters['sort'] ?? null)
            ->paginate($filters['per_page'] ?? 5)
            ->withQueryString();
    }

}
