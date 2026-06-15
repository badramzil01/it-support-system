<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [

        // =====================================
        // 👤 USER
        // =====================================
        'user_id',

        // =====================================
        // 💬 CONVERSATION
        // =====================================
        'conversation_id',

        // =====================================
        // 👥 SENDER
        // =====================================
        'sender',

        // =====================================
        // 📝 MESSAGE USER
        // =====================================
        'content',

        // =====================================
        // 🤖 RESPONSE BOT
        // =====================================
        'response',

        // =====================================
        // 🌐 CHANNEL
        // =====================================
        'channel',

        // =====================================
        // 📡 SOURCE
        // =====================================
        'source',

        // =====================================
        // 📊 STATUS
        // =====================================
        'status',

        'read_at',

        'support_agent_id',
        
 	   // =====================================
        // 📊 user_message
        // =====================================
        'user_message',


        // =====================================
        // 🖼 IMAGE
        // =====================================
        'image_path',
        'mime_type'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // =========================================
    // 👤 USER RELATION
    // =========================================
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // =========================================
    // 🎫 TICKET RELATION
    // =========================================
    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }

    public function triggeredTicket()
    {
        return $this->hasOne(Ticket::class, 'trigger_message_id');
    }

    public function supportAgent()
    {
        return $this->belongsTo(User::class, 'support_agent_id');
    }

    // =========================================
    // 💬 CONVERSATION RELATION
    // =========================================
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
