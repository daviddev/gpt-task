<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use OpenAI;

class AIService
{
    /**
     * OpenAI client.
     *
     * @var OpenAI\Client $client
     */
    private OpenAI\Client $client;

    public function __construct()
    {
        $this->client = OpenAI::client(getenv('OPEN_AI_KEY'));
    }

    /**
     * Answer question using OpenAi.
     *
     * @param string $question
     * @param string $text
     * @return string|null
     */
    public function answerQuestion(string $question, string $text): ?string
    {
        try {
            $result = $this->client->chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Your responses should rely heavily on the context provided in the following messages to ensure accuracy.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $text,
                    ],
                    [
                        'role' => 'assistant',
                        'content' => 'Understood.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $question . 'Please answer yes or no.',
                    ],
                ],
            ]);
            if (isset($result['choices'][0]['message']['content'])) {
                $answer = $result['choices'][0]['message']['content'];
                if (in_array(strtolower($answer), ['yes', 'no'])) {
                    return $answer;
                }
            }
            return null;
        } catch (Exception $e) {
            Log::error('ChatGPT An error occurred while answering questions', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }

        return $text;
    }
}
