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

class Project extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'status',
        'stage',
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
     | Constants — Legacy project status (kept for compat)
     | -------------------------------------------------------- */

    public const STATUS_ACTIVE    = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ARCHIVED  = 'archived';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_COMPLETED,
        self::STATUS_ARCHIVED,
    ];

    /* --------------------------------------------------------
     | Constants — Project Stages (new sales/delivery pipeline)
     | -------------------------------------------------------- */

    public const STAGE_APPROACH     = 'approach_lead_client';
    public const STAGE_PROPOSAL     = 'proposal';
    public const STAGE_HOLD_BILLING = 'hold_billing';
    public const STAGE_PROGRESS     = 'project_progress';
    public const STAGE_HANDOVER     = 'project_handover';

    public const STAGES = [
        self::STAGE_APPROACH     => 'Approach & Lead Client',
        self::STAGE_PROPOSAL     => 'Proposal',
        self::STAGE_HOLD_BILLING => 'Hold & Billing',
        self::STAGE_PROGRESS     => 'Project Progress',
        self::STAGE_HANDOVER     => 'Project Handover',
    ];

    /**
     * Stage badge color tokens (Tailwind classes).
     */
    public const STAGE_COLORS = [
        self::STAGE_APPROACH     => ['bg' => 'bg-blue-500/15',   'text' => 'text-blue-400',   'border' => 'border-blue-500/20'],
        self::STAGE_PROPOSAL     => ['bg' => 'bg-violet-500/15', 'text' => 'text-violet-400', 'border' => 'border-violet-500/20'],
        self::STAGE_HOLD_BILLING => ['bg' => 'bg-yellow-500/15', 'text' => 'text-yellow-400', 'border' => 'border-yellow-500/20'],
        self::STAGE_PROGRESS     => ['bg' => 'bg-emerald-500/15','text' => 'text-emerald-400','border' => 'border-emerald-500/20'],
        self::STAGE_HANDOVER     => ['bg' => 'bg-surface-3',     'text' => 'text-ink-subtle',  'border' => 'border-hairline'],
    ];

    /**
     * Get the human-readable label for the current stage.
     */
    public function getStageLabelAttribute(): string
    {
        $stage = ProjectStage::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant_id)
            ->where('key', $this->stage)
            ->first();

        if ($stage) {
            return $stage->name;
        }

        return self::STAGES[$this->stage] ?? ucfirst(str_replace('_', ' ', $this->stage));
    }

    /* --------------------------------------------------------
     | Relationships
     | -------------------------------------------------------- */

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('tenant_id')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Stages are now global per-tenant, not per-project.
    public function stages(): HasMany
    {
        return $this->hasMany(WorkflowStage::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Get the tenant-wide workflow stages for this project's tenant.
     */
    public function tenantStages()
    {
        return WorkflowStage::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant_id)
            ->whereNull('project_id')
            ->orderBy('sort_order')
            ->get();
    }

    /* --------------------------------------------------------
     | Query Scopes
     | -------------------------------------------------------- */

    public function scopeAccessibleBy(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin() || $user->isCompanyAdmin()) {
            return $query;
        }

        return $query->where('owner_id', $user->id)
            ->orWhereHas('members', fn(Builder $q) => $q->where('users.id', $user->id));
    }

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

    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeFilterStage(Builder $query, ?string $stage): Builder
    {
        if (blank($stage)) {
            return $query;
        }

        return $query->where('stage', $stage);
    }
}