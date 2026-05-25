<?php

namespace App\Services;

use OpenAI;

class AIService
{
    public function ask($message)
    {
        try {
            $client = OpenAI::client(env('OPENAI_API_KEY'));

            $response = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are an IT support expert.

Give clear, step-by-step solutions.
Be practical.
Answer in French if user speaks French."
                    ],
                    [
                        'role' => 'user',
                        'content' => $message
                    ]
                ],
            ]);

            return $response->choices[0]->message->content;

        } catch (\Exception $e) {
            return "Erreur AI: " . $e->getMessage();
        }
    }
}