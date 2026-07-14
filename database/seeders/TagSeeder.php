<?php
namespace Database\Seeders;

use App\Models\Tag\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::create(
            ['name' => 'Adventure'],
        );

        Tag::create(
            ['name' => 'Science'],
        );

        Tag::create(
            ['name' => 'History'],
        );
    }
}
