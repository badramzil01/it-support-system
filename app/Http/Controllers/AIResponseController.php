<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AIResponse;

class AIResponseController extends Controller
{
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([

                'conversation_id' => 'nullable|integer',

                'user_id' => 'nullable|integer',

                'message' => 'nullable|string',

                'ai_response' => 'nullable|string',

                'source' => 'nullable|string',

                'priority' => 'nullable|string',

                'category' => 'nullable|string',

                'reason' => 'nullable|string',

                'has_image' => 'nullable|boolean',

                'image_url' => 'nullable|string',

                'is_urgent' => 'nullable|boolean',

                'is_escalated' => 'nullable|boolean',

                'create_ticket' => 'nullable|boolean',

            ]);

            $response = AIResponse::create([

                'conversation_id' => $validated['conversation_id'] ?? 0,

                'user_id' => $validated['user_id'] ?? 1,

                'message' => $validated['message'] ?? '',

                'ai_response' => $validated['ai_response'] ?? '',

                'source' => $validated['source'] ?? 'ai',

                'priority' => $validated['priority'] ?? 'medium',

                'category' => $validated['category'] ?? 'general',

                'reason' => $validated['reason'] ?? '',

                'has_image' => $validated['has_image'] ?? false,

                'image_url' => $validated['image_url'] ?? '',

                'is_urgent' => $validated['is_urgent'] ?? false,

                'is_escalated' => $validated['is_escalated'] ?? false,

                'create_ticket' => $validated['create_ticket'] ?? false,

            ]);

            return response()->json([

                'success' => true,

                'data' => $response

            ], 201);

        } catch (\Exception $e) {

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),

            ], 500);
        }
    }
}