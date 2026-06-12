<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedNote extends Model
{
    protected $fillable = [
        'note_id',
        'style_type',
        'content_markdown',
    ];

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }
}
