<?php

namespace Database\Seeders;

use App\Models\Blog\Article;
use App\Models\Blog\Blog;
use Illuminate\Database\Seeder;

class ArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Blog::factory()->create();
        Article::factory()->count(20)->create();
    }
}
