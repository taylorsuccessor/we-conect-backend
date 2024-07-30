<?php

namespace App\Http\Resources;

use App\Models\Blog\Article;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->resource->id,
            "title" => $this->resource->title,
            "content" => $this->resource->content,
            'user_id' =>  $this->resource->user_id,
            'blog' => BlogResource::make($this->whenLoaded('blog')),
            $this->merge(
                Arr::except(
                    parent::toArray($request),
                    [
                    'blog_id', 'user_id'
                    ]
                )
            )
        ];
    }
}
