<?php

namespace App\Models\Blog;

use App\Models\AbstractModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $name
 */
class Blog extends AbstractModel
{
    use HasFactory;


    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
    ];

    protected $table = 'blogs';
    protected $guarded = [];
    protected $hidden = [];
    public $translatable = [];
    public $timestamps = true;
    public $softDeleting = true;

    protected $searchables = [
        'name',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
