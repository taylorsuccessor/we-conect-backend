<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Models\Blog\Article;
use App\Models\Blog\Blog;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolePermissionSeeder;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setup(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create();
        $adminRole = Role::where(['name' => 'admin'])->first();
        $user->assignRole($adminRole);
        $this->actingAs($user);
    }

    #[Test]
    public function itListsArticles()
    {
        $user = User::factory()->create();
        $secondUser = User::factory()->create();
        $blog = Blog::factory()->create();

        $article = Article::factory()->create(
            [
            'blog_id' => $blog->id,
            'user_id' => $secondUser->id,
            'title' => 'My first article title',
            'content' => "some content for the article",
            ]
        );

        $articles = Article::factory()->count(1)->create(
            [
            'blog_id' => $blog->id,
            'user_id' => $user->id,
            'title' => "second-article",
            'content' => "second article content"
            ]
        );

        $this->getJson(route('articles.index'))->assertSee(
            [
            $article->id,
            ...$articles->pluck('id')->toArray()
            ]
        );

        $this->assertCount(
            1,
            $this->getJson(route('articles.index') . '?filter[title]=first')
                ->json('data')
        );
        $this->assertCount(
            1,
            $this->getJson(route('articles.index') . '?filter[user_id]=' . $secondUser->id)
                ->json('data')
        );
    }

    #[Test]
    public function itDoesntIncludeRelationshipsByDefault()
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create();

        $article = Article::factory()->create(
            [
            'user_id' => $user->id,
            'blog_id' => $blog->id
            ]
        );

        $response = $this->getJson(route('articles.index'));

        $this->assertArrayNotHasKey('user', $response->json('data')[0]);
        $this->assertArrayNotHasKey('blog', $response->json('data')[0]);
    }

    #[Test]
    public function itIncludesRelationships()
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create();

        $article = Article::factory()->create(
            [
            'user_id' => $user->id,
            'blog_id' => $blog->id
            ]
        );

        $response = $this->getJson(route('articles.index') . '?include=blog,user');

        $responseArticle = collect($response->json('data'))->first(fn($i) => $i['id'] === $article->id);

        $this->assertEquals($user->id, $responseArticle['user']['id']);
        $this->assertEquals($blog->id, $responseArticle['blog']['id']);
    }

    #[Test]
    public function itArticlesListFilterSoftDeletion()
    {
        $articles = Article::factory(2)->create();

        $articles[0]->delete();

        $this->getJson(route('articles.index') . '?filter[is_deleted]=0')->assertDontSee($articles[0]->id);
    }

    #[Test]
    public function itShowsArticle()
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create();

        $article = Article::factory()->create(
            [
            'user_id' => $user->id,
            'blog_id' => $blog->id
            ]
        );
        $response = $this->getJson(route('articles.show', ['article' => $article]));

        $this->assertEquals($user->id, $response->json('data')['user']['id']);
        $this->assertEquals($blog->id, $response->json('data')['blog']['id']);
    }

    #[Test]
    public function itShows404IfArticleNotFound()
    {
        $this->putJson(route('articles.show', 'incorrect'))->assertStatus(404);
    }

    #[Test]
    public function itShows404IfArticleToUpdateNotFound()
    {
        $this->putJson(route('articles.update', ['article' => '-122']))->assertStatus(404);
    }

    #[Test]
    public function itCreatesNewArticle()
    {

        Storage::fake('public');

        Article::factory()->create();
        $user = user::factory()->create();

        $blog = Blog::factory()->create();

        $response = $this->postJson(
            route('articles.create'), [
                'title' => 'my_article_title',
                'blog_id' => $blog->id,
                'user_id' => $user->id,
                'content' => "my content for the article",
                'article_cover_img' => UploadedFile::fake()->image('article_cover.jpg')
            ]
        );

        $response->assertSuccessful();

        $this->assertArrayHasKey('id', $response->json('data'));
        $this->assertTrue(Str::isUuid($response->json('data')['id']));

        $showResponse = $this->getJson(route('articles.show', ['article' => $response->json('data')['id']]));

        $this->assertEquals('my_article_title', $showResponse->json('data')['title']);
        $this->assertStringContainsString('.jpg', $showResponse->json('data')['article_cover_img']);

        Storage::assertExists($showResponse->json('data')['article_cover_img']);
    }

    #[Test]
    public function itUploadArticleImage()
    {

        Storage::fake('public');

        $user = user::factory()->create();
        $blog = Blog::factory()->create();

        $response = $this->postJson(
            route('articles.create'), [
                'title' => 'my_article_title',
                'blog_id' => $blog->id,
                'user_id' => $user->id,
                'content' => "this content for the article",
                'article_cover_img' => UploadedFile::fake()->image('article_cover.jpg')
            ]
        );

        $response->assertSuccessful();

        $article = Article::first();
        $this->assertNotNull($article->article_cover_img);
        Storage::assertExists( $article->article_cover_img);
    }

    #[Test]
    public function itValidatesCreateArticleFields()
    {
        $response = $this->postJson(
            route('articles.create'), [
            'title' => '',
            'content' => ''
            ]
        );
        $response->assertStatus(422);

        $this->assertArrayHasKey('title', $response->json('errors'));
        $this->assertArrayHasKey('content', $response->json('errors'));
        $this->assertCount(2, $response->json('errors'));
    }

    #[Test]
    public function itUpdatesArticle()
    {

        $user = user::factory()->create();
        $blog = Blog::factory()->create();

        $article = Article::factory()->create(['title' => 'title to update']);

        $response = $this->putJson(
            route('articles.update', ['article' => $article]), [
            'title' => 'my_article_title',
            'blog_id' => $blog->id,
            'user_id' => $user->id,
            'content' => "my content"
            ]
        );

        $response->assertSuccessful();

        $showResponse = $this->getJson(route('articles.show', ['article' => $article]));

        $this->assertEquals($user->id, $showResponse->json('data')['user']['id']);
        $this->assertEquals($blog->id, $showResponse->json('data')['blog']['id']);
        $this->assertEquals('my_article_title', $showResponse->json('data')['title']);
    }

    #[Test]
    public function itDeletesArticle()
    {
        $article = Article::factory()->create();

        $this->delete(route('articles.delete', ['article' => $article]));

        $article->refresh();

        $this->assertNotNull($article->deleted_at);
    }

    #[Test]
    public function itCanAccessViewIfAuthorized()
    {
        $user = User::factory()->create();
        $permission = Permission::where(['name' => 'article.view', 'guard_name' => 'api'])->first(); // Replace with your permission name

        $this->actingAs($user);
        $user = User::factory()->create();
        $blog = Blog::factory()->create();

        $article = Article::factory()->create(
            [
            'user_id' => $user->id,
            'blog_id' => $blog->id
            ]
        );

        $response = $this->getJson(route('articles.show', ['article' => $article]));

        $response->assertStatus(403);
        $this->actingAs($user);

        $user->givePermissionTo($permission);

        $response = $this->getJson(route('articles.show', ['article' => $article]));

        $response->assertSuccessful();
    }

}
