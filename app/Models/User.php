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
        'phone',
        'profile_photo_path',
        'dark_mode',
        'last_login_at',
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
            'dark_mode' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * ─── RELATIONS ───────────────────────────────────────────────
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function knowledgeBaseItems()
    {
        return $this->hasMany(KnowledgeBase::class, 'author_id');
    }

    public function knowledgeBaseModifications()
    {
        return $this->hasMany(KnowledgeBase::class, 'last_modified_by');
    }

    public function sentInternalMessages()
    {
        return $this->hasMany(InternalMessage::class, 'sender_id');
    }

    public function receivedInternalMessages()
    {
        return $this->hasMany(InternalMessage::class, 'receiver_id');
    }

    public function unreadInternalMessages()
    {
        return $this->receivedInternalMessages()->unread();
    }

    /**
     * ─── ROLE HELPERS ────────────────────────────────────────────
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

    /**
     * ─── PERMISSION HELPERS ──────────────────────────────────────
     * Admin always bypasses all permission checks.
     */

    /**
     * Check if the user has a specific permission (admin bypasses).
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->hasPermissionTo($permission);
    }

    /**
     * Check if the user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->hasAnyPermission($permissions);
    }

    /**
     * Get all permissions grouped by category for the user.
     */
    public function getPermissionsByGroup(): array
    {
        $grouped = \App\Services\PermissionService::getGrouped();
        $userPermissions = $this->getAllPermissions()->pluck('name')->toArray();

        $result = [];
        foreach ($grouped as $group => $permissions) {
            $result[$group] = array_map(fn($perm) => [
                'name' => $perm,
                'granted' => in_array($perm, $userPermissions),
            ], $permissions);
        }

        return $result;
    }
}
