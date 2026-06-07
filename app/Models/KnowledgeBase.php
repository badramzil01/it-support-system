<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $table = 'knowledge_base';

    protected $fillable = [
        'problem_keywords',
        'solution',
        'source',
        'usage_count',
        'category',
        'author_id'
    ];

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'author_id');
    }
}