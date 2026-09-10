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
     * Translate an associative array of field values from one language to another.
     * $direction: 'en_to_ar' or 'ar_to_en'
     * $fields = ['title' => '...', 'description' => '...']
     * Returns the same keys, translated.
     */
    public function translateFields(array $fields, string $direction = 'en_to_ar'): array
    {
        $fields = array_filter($fields, fn($v) => filled($v));

        if (empty($fields)) {
            return [];
        }

        $response = Http::timeout(30)
            ->withHeaders(['x-goog-api-key' => $this->apiKey])
            ->post('https://generativelanguage.googleapis.com/v1beta/interactions', [
                'model' => $this->model,
                'input' => $this->buildPrompt($fields, $direction),
                'response_format' => $this->buildResponseFormat($fields),
            ]);

        if ($response->failed()) {
            Log::error('Gemini translation request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Translation service is currently unavailable. Please try again.');
        }

        // Interactions API returns a `steps` array; the actual generated
        // text lives in the step with type "model_output".
        $steps = data_get($response->json(), 'steps', []);
        $modelOutput = collect($steps)->firstWhere('type', 'model_output');
        $text = data_get($modelOutput, 'content.0.text');

        $translated = json_decode((string) $text, true);

        if (! is_array($translated)) {
            Log::error('Gemini returned an unparsable translation response', [
                'raw' => $text,
                'full_response' => $response->json(),
            ]);
            throw new RuntimeException('Translation service returned an unexpected response.');
        }

        return $translated;
    }

    /**
     * Build a JSON schema matching the keys we sent, so response_format
     * can enforce the same shape back.
     */
    protected function buildResponseFormat(array $fields): array
    {
        $properties = [];
        foreach (array_keys($fields) as $key) {
            $properties[$key] = ['type' => 'string'];
        }

        return [
            'type' => 'text',
            'mime_type' => 'application/json',
            'schema' => [
                'type' => 'object',
                'properties' => $properties,
                'required' => array_keys($fields),
            ],
        ];
    }

    protected function buildPrompt(array $fields, string $direction): string
    {
        $fieldList = json_encode($fields, JSON_UNESCAPED_UNICODE);

        $languageInstruction = $direction === 'ar_to_en'
            ? 'Translate the values in the following JSON object from Arabic into natural, professional English.'
            : 'Translate the values in the following JSON object from English into natural, professional Modern Standard Arabic.';

        return <<<PROMPT
You are a professional translator for Media Link International (MLI), a Beirut-based Arabic TV and content distribution company. {$languageInstruction} The content is for a media/broadcast audience — preserve tone and meaning, do not add commentary.

Return ONLY a valid JSON object with the exact same keys, containing the translations as values. No markdown, no explanation, no extra text.

Input:
{$fieldList}
PROMPT;
    }
}
