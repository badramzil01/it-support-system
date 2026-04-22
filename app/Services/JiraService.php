<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class JiraService
{
    public function createTicket($summary, $description)
    {
        $url = env('JIRA_URL') . '/rest/api/3/issue';

        $response = Http::withBasicAuth(
            env('JIRA_EMAIL'),
            env('JIRA_API_TOKEN')
        )->post($url, [
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
                                    "text" => $description,
                                    "type" => "text"
                                ]
                            ]
                        ]
                    ]
                ],
                "issuetype" => [
                    "name" => "Task"
                ]
            ]
        ]);

        return $response->json();
    }
}