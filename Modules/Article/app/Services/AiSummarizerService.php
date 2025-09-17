<?php

namespace Modules\Article\Services;

use Modules\Article\Models\Article;
use Modules\Article\Services\Contracts\AiSummarizerServiceInterface;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

class AiSummarizerService implements AiSummarizerServiceInterface
{
    public function summarize(Article $article): string
    {
        $aiResponse = '';

        $systemPrompt = '
        You are a professional text summarizer.
        Summarize the provided text as briefly and concisely as possible.
        Focus only on main ideas and key facts.
        Return the summary in 1-3 sentences.
        Do not add introductions, conclusions, or extra commentary.
        ';

        $userPrompt = "
        Summarize the following article without adding any extra introductory phrases or comments:
        {$article->content}
        ";

        $response = Prism::text()
            ->using(Provider::Ollama, 'llama2:latest')
            ->withSystemPrompt($systemPrompt)
            ->withPrompt($userPrompt)
            ->withMaxTokens(300)
            ->withClientOptions(['timeout' => 300])
            ->asStream();

        foreach ($response as $chunk) {
            $aiResponse .= $chunk->text;
            ob_flush();
            flush();
        }

        return $aiResponse;
    }
}
