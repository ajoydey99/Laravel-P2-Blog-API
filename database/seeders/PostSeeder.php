<?php
namespace Database\Seeders;

use App\Models\Post\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::create([
            'title'   => 'first_post',
            'body'    => 'first post body',
            'views'   => 1,
            'user_id' => 1,
        ]);
    }
}