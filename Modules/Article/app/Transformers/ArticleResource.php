<?php

namespace Modules\Article\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function __construct($resource, $aiSummay = null)
    {
        parent::__construct($resource);
        $this->aiSummary = $aiSummay;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => preg_replace('/\s+/', ' ', $this->content),
            'ai_short' => $this->aiSummary ? preg_replace('/\s+/', ' ', $this->aiSummary) : null,
            'image' => $this->image,
            'author' => $this->author ? [
                'author_name' => $this->author->username,
            ] : null,
            'category' => $this->category ? [
                'category_name' => $this->category->name,
                'category_slug' => $this->category->slug,
            ] : null,
            'tags' => $this->tags->pluck('name'),
            'likes' => $this->likesCount(),
            'dislikes' => $this->dislikesCount(),
            'is_published' => $this->is_published,
            'published_at' => Carbon::parse($this->published_at)->diffForHumans(),
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            'updated_at' => Carbon::parse($this->updated_at)->diffForHumans(),
            'premium' => $this->is_premium,
        ], fn ($value) => ! is_null($value));
    }
}
