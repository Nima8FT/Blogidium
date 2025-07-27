<?php

namespace Modules\Article\Models;

use App\Models\User;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Article\Database\Factories\ArticleFactory;
use Modules\Category\Models\Category;
use Modules\SocialNetwork\Models\Comment;
use Modules\Tag\Models\Tag;

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

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tag', 'article_id', 'tag_id');
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'likes')->withPivot('like')->withTimestamps();
    }

    public function likesCount()
    {
        return $this->likedByUsers()->wherePivot('like', true)->count();
    }

    public function dislikesCount()
    {
        return $this->likedByUsers()->wherePivot('like', false)->count();
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saves')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
