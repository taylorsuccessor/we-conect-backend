<?php

namespace App\Models\Blog;

use App\Models\AbstractModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends AbstractModel
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'blog_id',
        'title',
        'content',
        'article_cover_img'
    ];

    protected $table = 'articles';
    protected $guarded = [];
    protected $hidden = [];
    public $translatable = [];
    public $timestamps = true;
    public $softDeleting = true;

    protected $searchables = [
        'user_id',
        'title',
    ];

    protected $casts = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
