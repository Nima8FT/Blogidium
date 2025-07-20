<?php

namespace Modules\Category\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;
use Modules\Category\Database\Factories\CategoryFactory;

class Category extends Model
{
    use HasFactory, Sluggable, softDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'is_active'];

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
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

    // not deleted when category has children
    protected static function booted()
    {
        static::deleting(function ($category) {
            if ($category->children()->exists()) {
                throw ValidationException::withMessages([
                    'category' => 'Cannot delete category with child categories.',
                ]);
            }
        });
    }
}
