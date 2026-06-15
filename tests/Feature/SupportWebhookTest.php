<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SupportWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_sends_required_fields_to_n8n()
    {
        // Fake n8n webhook response
        Http::fake([
            '*/webhook-test/support' => Http::response([
                'response' => [
                    'message' => 'Le serveur principal est en panne.',
                    'is_urgent' => true,
                    'is_escalated' => true,
                    'create_ticket' => true,
                    'priority' => 'critical',
                    'category' => 'Infrastructure',
                    'reason' => 'Le serveur principal est hors service.'
                ]
            ], 200),
            '*/webhook/jira-ticket' => Http::response([
                'jira_ticket_id' => 'SUP-123'
            ], 200)
        ]);

        // Create a user
        $user = User::factory()->create([
            'email' => 'user@example.com'
        ]);

        // Act
        $response = $this->actingAs($user)->postJson('/api/webhook/support', [
            'message' => 'Mon serveur principal est en panne',
            'priority' => 'high',
            'user_id' => $user->id,
            'customer_email' => 'user@example.com',
            'source' => 'web',
            'is_urgent' => true,
            'is_escalated' => true,
            'create_ticket' => true
        ]);

        // Assert response status
        $response->assertStatus(200);

        // Assert JSON response structure
        $response->assertJson([
            'success' => true,
            'is_urgent' => true,
            'is_escalated' => true,
            'create_ticket' => true,
            'priority' => 'critical',
            'category' => 'Infrastructure'
        ]);

        // Assert HTTP payload sent to n8n has correct keys
        Http::assertSent(function ($request) use ($user) {
            return $request->url() === 'http://localhost:5678/webhook/support' &&
                $request['conversation_id'] !== null &&
                $request['user_id'] === $user->id &&
                $request['customer_email'] === 'user@example.com' &&
                $request['user_message'] === 'Mon serveur principal est en panne' &&
                $request['message'] === 'Mon serveur principal est en panne' &&
                $request['priority'] === 'high' &&
                $request['category'] === 'general' &&
                $request['source'] === 'web' &&
                $request['is_urgent'] === true &&
                $request['is_escalated'] === true &&
                $request['create_ticket'] === true &&
                $request['image_url'] === '' &&
                $request['image_base64'] === '' &&
                $request['mime_type'] === '' &&
                $request['has_image'] === false &&
                is_array($request['messages']);
        });
    }

    public function test_webhook_sends_image_fields_to_n8n()
    {
        // Fake storage
        \Illuminate\Support\Facades\Storage::fake('public');

        // Fake n8n webhook response
        Http::fake([
            '*/webhook-test/support' => Http::response([
                'response' => [
                    'message' => 'Analyse du screenshot.',
                    'is_urgent' => false,
                    'is_escalated' => false,
                    'create_ticket' => false,
                    'priority' => 'medium',
                    'category' => 'screenshot_analysis',
                    'reason' => 'Analyse visuelle.'
                ]
            ], 200)
        ]);

        // Create a user
        $user = User::factory()->create([
            'email' => 'user@example.com'
        ]);

        // Create fake image file
        $file = \Illuminate\Http\UploadedFile::fake()->create('screenshot.png', 100, 'image/png');

        // Act
        $response = $this->actingAs($user)->postJson('/api/webhook/support', [
            'message' => 'Voici ma capture',
            'image' => $file,
            'priority' => 'medium',
            'user_id' => $user->id,
            'customer_email' => 'user@example.com',
            'source' => 'web',
            'is_urgent' => false,
            'is_escalated' => false,
            'create_ticket' => false
        ]);

        // Assert response status
        $response->assertStatus(200);

        // Assert HTTP payload sent to n8n has correct image details
        Http::assertSent(function ($request) use ($user) {
            return $request->url() === 'http://localhost:5678/webhook/support' &&
                $request['conversation_id'] !== null &&
                $request['user_id'] === $user->id &&
                $request['customer_email'] === 'user@example.com' &&
                $request['user_message'] === 'Voici ma capture' &&
                $request['message'] === 'Voici ma capture' &&
                $request['is_urgent'] === false &&
                $request['is_escalated'] === false &&
                $request['create_ticket'] === false &&
                $request['has_image'] === true &&
                $request['image_url'] !== '' &&
                isset($request['image_base64']) &&
                $request['mime_type'] === 'image/png';
        });
    }
}
