<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\WorkflowStage;
use App\Models\ProjectInvitation;

class Project extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
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

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class , 'project_members')
            ->withPivot('tenant_id')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(ProjectInvitation::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(WorkflowStage::class)->orderBy('sort_order')->orderBy('id');
    }

    protected static function booted(): void
    {
        static::created(function (Project $project): void {
            $project->ensureDefaultStages();
        });
    }

    public function ensureDefaultStages(): void
    {
        if ($this->stages()->exists()) {
            return;
        }

        foreach ($this->defaultStages() as $stage) {
            $this->stages()->create(array_merge($stage, [
                'tenant_id' => $this->tenant_id,
            ]));
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function defaultStages(): array
    {
        return [
            ['name' => 'Backlog', 'key' => 'todo', 'sort_order' => 1],
            ['name' => 'In Progress', 'key' => 'in_progress', 'sort_order' => 2],
            ['name' => 'Done', 'key' => 'done', 'sort_order' => 3],
        ];
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