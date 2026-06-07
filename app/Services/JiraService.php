<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JiraService
{
    protected string $baseUrl;
    protected string $email;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('JIRA_URL'), '/');
        $this->email = env('JIRA_EMAIL');
        $this->token = env('JIRA_API_TOKEN');
    }

    public function createTicket($summary, $description)
    {
        $response = Http::withBasicAuth(
            $this->email,
            $this->token
        )->post(
            $this->baseUrl . '/rest/api/3/issue',
            [
                "fields" => [
                    "project" => [
                        "key" => env('JIRA_PROJECT_KEY')
                    ],
                    "summary" => $summary,
                    "description" => [
                        "type" => "doc",
                        "version" => 1,
                        "content" => [
                            [
                                "type" => "paragraph",
                                "content" => [
                                    [
                                        "type" => "text",
                                        "text" => $description
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "issuetype" => [
                        "name" => "Task"
                    ]
                ]
            ]
        );

        Log::info('jira.create.response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return $response->json();
    }

    public function getTransitions(string $issueKey)
    {
        $response = Http::withBasicAuth(
            $this->email,
            $this->token
        )->get(
            $this->baseUrl .
            '/rest/api/3/issue/' .
            $issueKey .
            '/transitions'
        );

        Log::info('jira.transitions', [
            'issue' => $issueKey,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return $response;
    }

    public function updateTicketStatus(
        string $issueKey,
        string $transitionId
    ): bool {

        Log::info('jira.transition.start', [
            'issue' => $issueKey,
            'transition' => $transitionId,
        ]);

        $response = Http::withBasicAuth(
            $this->email,
            $this->token
        )->post(
            $this->baseUrl .
            '/rest/api/3/issue/' .
            $issueKey .
            '/transitions',
            [
                'transition' => [
                    'id' => $transitionId
                ]
            ]
        );

        Log::info('jira.transition.response', [
            'issue' => $issueKey,
            'transition' => $transitionId,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return $response->successful();
    }

    public function moveToTodo(string $issueKey): bool
    {
        Log::info('MOVE TO TODO', [
            'issue' => $issueKey,
            'transition' => env('JIRA_TRANSITION_TODO')
        ]);

        return $this->updateTicketStatus(
            $issueKey,
            env('JIRA_TRANSITION_TODO')
        );
    }

    public function moveToInProgress(string $issueKey): bool
    {
        Log::info('MOVE TO IN PROGRESS', [
            'issue' => $issueKey,
            'transition' => env('JIRA_TRANSITION_IN_PROGRESS')
        ]);

        return $this->updateTicketStatus(
            $issueKey,
            env('JIRA_TRANSITION_IN_PROGRESS')
        );
    }

    public function moveToDone(string $issueKey): bool
    {
        Log::info('MOVE TO DONE', [
            'issue' => $issueKey,
            'transition' => env('JIRA_TRANSITION_DONE')
        ]);

        return $this->updateTicketStatus(
            $issueKey,
            env('JIRA_TRANSITION_DONE')
        );
    }
}