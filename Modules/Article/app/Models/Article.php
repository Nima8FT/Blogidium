<?php

namespace Modules\Article\Models;

use App\Models\User;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Article\Database\Factories\ArticleFactory;
use Modules\Category\Models\Category;

class Article extends Model
{
    use HasFactory, Sluggable, softDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['title', 'slug', 'content', 'image', 'author_id', 'category_id', 'is_published', 'published_at'];

    protected static function newFactory(): ArticleFactory
    {
        return ArticleFactory::new();
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'onUpdate' => true,
            ],
        ];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
