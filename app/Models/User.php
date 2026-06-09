<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Attributs modifiables
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Attributs cachés
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relations
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Conversations started by the user
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Solutions de la base de connaissance créées par cet utilisateur
     */
    public function knowledgeBaseItems()
    {
        return $this->hasMany(KnowledgeBase::class, 'author_id');
    }

    public function knowledgeBaseModifications()
    {
        return $this->hasMany(KnowledgeBase::class, 'last_modified_by');
    }

    /**
     * Internal messages sent by this user
     */
    public function sentInternalMessages()
    {
        return $this->hasMany(InternalMessage::class, 'sender_id');
    }

    /**
     * Internal messages received by this user
     */
    public function receivedInternalMessages()
    {
        return $this->hasMany(InternalMessage::class, 'receiver_id');
    }

    /**
     * Unread internal messages received by this user
     */
    public function unreadInternalMessages()
    {
        return $this->receivedInternalMessages()->unread();
    }

    /**
     * Helpers
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isSupport(): bool
    {
        return $this->hasRole('support');
    }

    public function isClient(): bool
    {
        return !$this->hasRole('admin') && !$this->hasRole('support');
    }
}
