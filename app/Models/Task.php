<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo as TaskBelongsTo;
use App\Models\WorkflowStage;

class Task extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'stage_id',
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
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

    public const STATUS_TODO = 'todo';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_DONE = 'done';

    // Legacy fallback – prefer dynamic stage keys from WorkflowStage
    public const STATUSES = [
        self::STATUS_TODO,
        self::STATUS_IN_PROGRESS,
        self::STATUS_IN_REVIEW,
        self::STATUS_DONE,
    ];

    /**
     * Get all valid status keys for the authenticated user's tenant.
     * Falls back to STATUSES constant if no stages exist.
     */
    public static function getValidStatuses(): array
    {
        $tenantId = auth()->user()?->tenant_id
            ?? session('superadmin_tenant_id');

        if (!$tenantId) {
            return self::STATUSES;
        }

        $keys = WorkflowStage::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->whereNull('project_id')
            ->pluck('key')
            ->toArray();

        return empty($keys) ? self::STATUSES : $keys;
    }

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH,
    ];

    /* --------------------------------------------------------
     | Relationships
     | -------------------------------------------------------- */

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function stage(): TaskBelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'stage_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class , 'assigned_to');
    }

    /* --------------------------------------------------------
     | Query Scopes
     | -------------------------------------------------------- */

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

    public function scopeFilterPriority(Builder $query, ?string $priority): Builder
    {
        if (blank($priority)) {
            return $query;
        }

        return $query->where('priority', $priority);
    }

    public function scopeFilterAssignee(Builder $query, ?int $assigneeId): Builder
    {
        if (is_null($assigneeId)) {
            return $query;
        }

        return $query->where('assigned_to', $assigneeId);
    }
}