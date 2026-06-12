<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStats extends Model
{
    protected $table = 'user_stats';
    
    protected $primaryKey = 'user_id';
    
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'total_notes',
        'total_quizzes_created',
        'total_quizzes_answered',
        'correct_answers',
        'incorrect_answers',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
