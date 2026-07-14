<?php
namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\PostSeeder;
use Database\Seeders\TagSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
            'role'  => 'user',
        ]);

        $this->call(PostSeeder::class);
        $this->call(TagSeeder::class);
    }
}