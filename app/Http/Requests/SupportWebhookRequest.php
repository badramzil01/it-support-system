<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupportWebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'message'         => 'nullable|string|max:3000',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_urgent'       => 'nullable|boolean',
            'is_escalated'    => 'nullable|boolean',
            'create_ticket'   => 'nullable|boolean',
            'priority'        => 'nullable|string|in:low,medium,high,critical',
            'source'          => 'nullable|string|max:40',
            'session_id'      => 'nullable|string|max:120',
            'conversation_id' => 'nullable',
            'user_id'         => 'nullable|integer',
            'customer_email'  => 'nullable|email|max:255',
        ];
    }

    /**
     * Prepare inputs for validation: normalize boolean flag strings to actual booleans.
     * Front-end can send "1", "0", "true", "false", true, false, 1, 0.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['is_urgent', 'is_escalated', 'create_ticket'] as $flag) {
            if ($this->has($flag)) {
                $raw = $this->input($flag);
                $merge[$flag] = filter_var($raw, FILTER_VALIDATE_BOOLEAN);
            }
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }
}
