<?php

namespace Modules\Tag\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Article\Models\Article;
use Modules\Tag\Database\Factories\TagFactory;

class Tag extends Model
{
    use HasFactory,sluggable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'slug'];

    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => true,
            ],
        ];
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_tag', 'article_id', 'tag_id');
    }
}
