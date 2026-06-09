<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $table = 'knowledge_base';

    protected $fillable = [
        'problem_keywords',
        'solution',
        'source',
        'usage_count',
        'category',
        'author_id',
        'last_modified_by',
        'status',
        'confidence',
        'tags',
    ];

    protected $casts = [
        'usage_count'    => 'integer',
        'confidence'     => 'float',
        'tags'           => 'array',
        'author_id'      => 'integer',
        'last_modified_by' => 'integer',
    ];

    // ============================================================
    // Relations
    // ============================================================

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function lastEditor()
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }

    // ============================================================
    // Scopes
    // ============================================================

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    public function scopeDraft($q)
    {
        return $q->where('status', 'draft');
    }

    public function scopeArchived($q)
    {
        return $q->where('status', 'archived');
    }

    // ============================================================
    // Accessors
    // ============================================================

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'Active',
            'draft'    => 'Brouillon',
            'archived' => 'Archivée',
            default    => ucfirst((string) $this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'emerald',
            'draft'    => 'amber',
            'archived' => 'slate',
            default    => 'slate',
        };
    }
}
