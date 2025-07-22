<?php

namespace Modules\Article\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:255|unique:articles,title',
            'content' => 'required|string|min:3',
            'category_id' => 'required|integer|exists:categories,id',
            'tags' => 'array',
            'tags.*' => 'integer|exists:tags,id',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
