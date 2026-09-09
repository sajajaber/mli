<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GeminiTranslationService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model');
    }

    /**
     * Translate an associative array of English field values into Arabic.
     * $fields = ['title' => 'Hello', 'description' => 'Some text']
     * Returns ['title' => '...', 'description' => '...']
     */
    public function translateFields(array $fields): array
    {
        $fields = array_filter($fields, fn($v) => filled($v));

        if (empty($fields)) {
            return [];
        }

        $response = Http::timeout(30)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}",
            [
                'contents' => [
                    ['parts' => [['text' => $this->buildPrompt($fields)]]],
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                ],
            ]
        );

        if ($response->failed()) {
            Log::error('Gemini translation request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Translation service is currently unavailable. Please try again.');
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
        $translated = json_decode((string) $text, true);

        if (! is_array($translated)) {
            Log::error('Gemini returned an unparsable translation response', ['raw' => $text]);
            throw new RuntimeException('Translation service returned an unexpected response.');
        }

        return $translated;
    }

    protected function buildPrompt(array $fields): string
    {
        $fieldList = json_encode($fields, JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
You are a professional English-to-Arabic translator for Media Link International (MLI), a Beirut-based Arabic TV and content distribution company. Translate the values in the following JSON object into natural, professional Modern Standard Arabic suitable for a media/broadcast audience. Preserve tone and meaning; do not add commentary.

Return ONLY a valid JSON object with the exact same keys, containing the Arabic translations as values. No markdown, no explanation, no extra text.

Input:
{$fieldList}
PROMPT;
    }
}
