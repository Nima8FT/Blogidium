<?php

namespace Modules\Article\Services\Contracts;

use Modules\Article\Models\Article;

interface AiSummarizerServiceInterface
{
    public function summarize(Article $article): string;
}
