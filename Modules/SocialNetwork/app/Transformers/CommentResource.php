<?php

namespace Modules\SocialNetwork\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return array_filter([
            'id' => $this->id,
            'content' => $this->content,
            'article' => $this->article ? [
                'id' => $this->article->id,
                'title' => $this->article->title,
            ] : null,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'username' => $this->user->username,
            ] : null,
            'reply' => $this->parent ? [
                'id' => $this->parent->id,
                'content' => $this->parent->content,
            ] : null,
            'status' => $this->status,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            'updated_at' => Carbon::parse($this->updated_at)->diffForHumans(),
        ], fn ($value) => ! is_null($value));
    }
}
