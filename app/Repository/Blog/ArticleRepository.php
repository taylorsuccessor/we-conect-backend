<?php

namespace App\Repository\Blog;

use App\Repository\AbstractRepository;
use App\Pay\PendingReviewRecords\PendingReviewRecord;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ArticleRepository extends AbstractRepository
{
    /**
     * @param  Article  $article
     * @param  array $attributes
     * @return array
     */
    protected function validate($article, $attributes)
    {
        $attributes = validator($attributes, [
            "blog_id" => ["nullable", Rule::exists("blogs", "id")],
            "user_id" => ["nullable", Rule::exists("users", "id")],
            "title" => [
                Rule::requiredIf(!$article->exists),
                "string",
                "max:64",
            ],
            "content" => [
                Rule::requiredIf(!$article->exists),
                "string",
                "max:500",
            ],
            "article_cover_img" => [
                "sometimes",
                "image",
                "mimes:jpeg,jpg,png",
                "max:2048",
            ],
        ])->validate();
        return $attributes;
    }

    /**
     * @param  Article  $article
     * @param  array $data
     * @return mixed|void
     */
    protected function store($article, $data)
    {
        if (request()->hasFile("article_cover_img")) {
            $data["article_cover_img"] = request()
                ->file("article_cover_img")
                ->store("article_images");
        }
        $article->fill(Arr::except($data, []))->save();

        return $article;
    }

    /**
     * @param  Article $article
     * @throws \Exception
     */
    public function delete($article)
    {
        $article->delete();
    }
}
