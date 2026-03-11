<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'owner_id',
        'deadline',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    /* --------------------------------------------------------
     | Constants
     | -------------------------------------------------------- */

    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_COMPLETED,
        self::STATUS_ARCHIVED,
    ];

    /* --------------------------------------------------------
     | Relationships
     | -------------------------------------------------------- */

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class , 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class , 'project_members')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /* --------------------------------------------------------
     | Query Scopes
     | -------------------------------------------------------- */

    /**
     * Scope: projects accessible by a given user (owned or member).
     */
    public function scopeAccessibleBy(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where('owner_id', $user->id)
            ->orWhereHas('members', fn(Builder $q) => $q->where('users.id', $user->id));
    }

    /**
     * Scope: search projects by title or description.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Scope: filter by status.
     */
    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }
}