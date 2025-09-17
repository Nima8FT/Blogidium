<?php

namespace Modules\Article\Tests\Unit;

use App\Models\User;
use Modules\Article\Models\Article;
use Modules\Article\Services\Contracts\AiSummarizerServiceInterface;
use Modules\Category\Models\Category;
use Tests\TestCase;

class AiSummarizeServiceTest extends TestCase
{
    public function test_ai_summarizer_service()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'author_id' => $user->id,
        ]);

        $mockAiService = $this->createMock(AiSummarizerServiceInterface::class);

        $mockAiService->method('summarize')->with($article)->willReturn('this is summary');

        $summary = $mockAiService->summarize($article);

        $this->assertEquals('this is summary', $summary);
    }
}
