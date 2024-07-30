<?php

namespace Database\Factories\Blog;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Blog\Article;
use App\Models\Blog\Blog;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'blog_id' => Blog::factory(),
            'title' => $this->faker->text(20),
            'content' => $this->faker->text(200)
        ];
    }
}
