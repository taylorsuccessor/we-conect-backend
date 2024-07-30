<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Blog\Article;
use App\Models\Blog\Blog;
use App\Repository\Blog\ArticleRepository;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Articles API",
 *         version="1.0.0"
 *     ),
 *      @OA\Components(
 *          @OA\SecurityScheme(
 *              securityScheme="customAuth",
 *              type="apiKey",
 *              scheme="bearer",
 *              description="Enter token in format  Bearer xx|xxxxxxxx",
 *              name="Authorization",
 *              in="header",
 *              scheme="Bearer"
 *          )
 *      )
 * )
 */
class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/article",
     *     summary=" Get list of articles",
     *     tags={"Articles"},
     *     security={{"sanctum":{}},{"customAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Filter by article ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filter by user ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Filter by article title",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of articles"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(Request $request)
    {
        return ArticleResource::collection(
            QueryBuilder::for(Article::class)
                ->withoutTrashed()
                ->defaultSort('-created_at')
                ->allowedFilters([
                    AllowedFilter::exact("id"),
                    AllowedFilter::exact("user_id"),
                    AllowedFilter::partial("title"),
                ])
                ->allowedSorts(["id", "created_at"])
                ->allowedIncludes(["user", "blog"])
                ->paginate(50)
        );
    }

    /**
     * @OA\Get(
     *     path="/api/article/{article}",
     *     summary="Get a article by ID",
     *     tags={"Articles"},
     *     security={{"sanctum":{}},{"customAuth":{}}},
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         description="ID of the article",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article details",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
    public function show(Article $article)
    {
        return ArticleResource::make($article->load(["blog", "user"]));
    }

    /**
     * @OA\Post(
     *     path="/api/article",
     *     summary="Create a new article",
     *     tags={"Articles"},
     *     security={{"sanctum":{}},{"customAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="title", type="string", maxLength=64, description="Required if article does not exist"),
     *                  @OA\Property(property="content", type="string", maxLength=500, nullable=false, maxLength=500),
     *                  @OA\Property(property="article_cover_img", type="string", format="binary", nullable=true, description="Image file in jpeg, jpg, or png format")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Article created",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function create()
    {
        return ArticleResource::make(
            (new ArticleRepository())
                ->create(
                    new Article(),
                    request()
                        ->merge([
                            "user_id" => auth()->id(),
                            "blog_id" => Blog::factory()->create()->id,
                        ])
                        ->all()
                )
                ->load([])
        );
    }

    /**
     * @OA\Post(
     *     path="/api/article/{article}",
     *     summary="Update an article",
     *     tags={"Articles"},
     *     security={{"sanctum":{}},{"customAuth":{}}},
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         description="ID of the article",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title", "content"},
     *                 @OA\Property(property="title", type="string", maxLength=64, description="Required if article does not exist"),
     *                 @OA\Property(property="content", type="string", maxLength=500, description="Required if article does not exist"),
     *                 @OA\Property(property="article_cover_img", type="string", format="binary", nullable=true, description="Image file in jpeg, jpg, or png format"),
     *                 @OA\Property(property="_method", type="string", example="PUT", description="Simulate a PUT request")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article updated"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
    public function update(Article $article)
    {
        return ArticleResource::make(
            (new ArticleRepository())
                ->update($article, request()->all())
                ->load([])
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/article/{article}",
     *     summary="Delete a article",
     *     tags={"Articles"},
     *     security={{"sanctum":{}},{"customAuth":{}}},
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         description="ID of the article",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     * @throws \Exception
     */
    public function delete(Article $article)
    {
        return (new ArticleRepository())->delete($article);
    }
}
