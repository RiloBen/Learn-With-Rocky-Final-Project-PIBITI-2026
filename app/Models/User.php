<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['username', 'password', 'theme_preference'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Relationships.
     */
    public function notes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function stats(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserStats::class, 'user_id');
    }

    public function quizzes(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Quiz::class, Note::class);
    }

    /**
     * Get the user's stats, creating them if they don't exist.
     */
    public function getStatsAttribute(): UserStats
    {
        if (!$this->relationLoaded('stats') || is_null($this->getRelation('stats'))) {
            $stats = $this->stats()->firstOrCreate();
            $this->setRelation('stats', $stats);
        }

        return $this->getRelation('stats');
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (User $user) {
            $user->stats()->create();
        });
    }
}
