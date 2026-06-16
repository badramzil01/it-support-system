<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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

    /**
     * Create a new Jira issue.
     */
    public function createTicket($summary, $description, $category = null, $priority = null)
    {
        $fields = [
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
            ],
        ];

        // Add category as labels if provided
        if ($category && $category !== 'general') {
            $fields["labels"] = [strtolower(str_replace(' ', '_', $category))];
        }

        // Add priority mapping if provided
        if ($priority) {
            $priorityMap = [
                'critical' => 'Highest',
                'high'     => 'High',
                'medium'   => 'Medium',
                'low'      => 'Low',
            ];
            $jiraPriority = $priorityMap[$priority] ?? null;
            if ($jiraPriority) {
                $fields["priority"] = ["name" => $jiraPriority];
            }
        }

        $response = Http::withBasicAuth(
            $this->email,
            $this->token
        )->post(
            $this->baseUrl . '/rest/api/3/issue',
            [
                "fields" => $fields
            ]
        );

        Log::info('jira.create.response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return $response->json();
    }

    /**
     * Get available transitions for an issue from Jira.
     */
    public function getTransitions(string $issueKey): array
    {
        $cacheKey = "jira_transitions_{$issueKey}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($issueKey) {
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

            if ($response->successful()) {
                return $response->json('transitions', []);
            }

            return [];
        });
    }

    /**
     * Find a transition ID by name (case-insensitive partial match).
     */
    public function findTransitionId(string $issueKey, string $statusName): ?string
    {
        $transitions = $this->getTransitions($issueKey);

        // Normalize the target status name
        $normalizedTarget = strtolower(trim($statusName));

        // Map our internal statuses to common Jira transition names
        $nameMap = [
            'open'        => ['todo', 'to do', 'backlog', 'open', ' reopen', 'reopened'],
            'in_progress' => ['in progress', 'start progress', 'start', 'doing'],
            'resolved'    => ['done', 'resolve', 'resolved', 'complete', 'closed', 'close'],
            'closed'      => ['done', 'close', 'closed', 'resolved', 'complete'],
        ];

        $searchTerms = $nameMap[$normalizedTarget] ?? [$normalizedTarget];

        foreach ($transitions as $transition) {
            $transitionName = strtolower($transition['name'] ?? '');

            foreach ($searchTerms as $term) {
                if (str_contains($transitionName, trim($term))) {
                    Log::info('jira.transition.found', [
                        'issue' => $issueKey,
                        'target_status' => $statusName,
                        'matched_name' => $transition['name'],
                        'transition_id' => $transition['id'],
                    ]);
                    return (string) $transition['id'];
                }
            }
        }

        // Fallback: try exact env variable IDs
        $envKeyMap = [
            'open'        => 'JIRA_TRANSITION_TODO',
            'in_progress' => 'JIRA_TRANSITION_IN_PROGRESS',
            'resolved'    => 'JIRA_TRANSITION_DONE',
            'closed'      => 'JIRA_TRANSITION_DONE',
        ];

        $envKey = $envKeyMap[$normalizedTarget] ?? null;
        if ($envKey && env($envKey)) {
            Log::info('jira.transition.fallback_env', [
                'issue' => $issueKey,
                'target_status' => $statusName,
                'env_key' => $envKey,
                'env_value' => env($envKey),
            ]);
            return (string) env($envKey);
        }

        Log::warning('jira.transition.not_found', [
            'issue' => $issueKey,
            'target_status' => $statusName,
            'available' => array_map(fn($t) => $t['name'] ?? '', $transitions),
        ]);

        return null;
    }

    /**
     * Update an issue's status by transitioning it.
     */
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

    /**
     * Move an issue to a status by name (dynamic transition lookup).
     */
    public function moveToStatus(string $issueKey, string $statusName): bool
    {
        // Clear cached transitions for this issue to ensure fresh data
        Cache::forget("jira_transitions_{$issueKey}");

        $transitionId = $this->findTransitionId($issueKey, $statusName);

        if ($transitionId === null) {
            Log::warning('jira.move.skip', [
                'issue' => $issueKey,
                'status' => $statusName,
                'reason' => 'No matching transition found',
            ]);
            return false;
        }

        return $this->updateTicketStatus($issueKey, $transitionId);
    }

    public function moveToTodo(string $issueKey): bool
    {
        return $this->moveToStatus($issueKey, 'open');
    }

    public function moveToInProgress(string $issueKey): bool
    {
        return $this->moveToStatus($issueKey, 'in_progress');
    }

    public function moveToDone(string $issueKey): bool
    {
        return $this->moveToStatus($issueKey, 'resolved');
    }

    /**
     * Update labels on an existing Jira issue.
     */
    public function updateLabels(string $issueKey, array $labels): bool
    {
        $response = Http::withBasicAuth(
            $this->email,
            $this->token
        )->put(
            $this->baseUrl . '/rest/api/3/issue/' . $issueKey,
            [
                'fields' => [
                    'labels' => $labels,
                ]
            ]
        );

        Log::info('jira.update_labels.response', [
            'issue' => $issueKey,
            'labels' => $labels,
            'status' => $response->status(),
        ]);

        return $response->successful();
    }
}