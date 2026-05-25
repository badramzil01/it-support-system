<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    // =========================================
    // ✅ MASS ASSIGNMENT
    // =========================================
    protected $fillable = [

        // 👤 USER
        'user_id',

        // 💬 TITLE
        'title',

        // 🏷 CURRENT CATEGORY
        'current_category',

        // 🧠 CURRENT PROBLEM
        'current_problem',

        // 🎫 TICKET CREATED
        'ticket_created',

        // 🎫 CURRENT TICKET ID
        'current_ticket_id',

        // 📊 CONVERSATION STATUS
        'conversation_status'
    ];

    // =========================================
    // 💬 RELATION MESSAGES
    // Maintenant utiliser seulement
    // la table messages
    // =========================================
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // =========================================
    // 👤 USER RELATION
    // =========================================
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}