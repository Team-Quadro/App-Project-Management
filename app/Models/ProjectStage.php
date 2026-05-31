<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStage extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'key',
        'sort_order',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public const DEFAULT_STAGES = [
        ['key' => Project::STAGE_APPROACH, 'name' => 'Approach & Lead Client', 'sort_order' => 1],
        ['key' => Project::STAGE_PROPOSAL, 'name' => 'Proposal', 'sort_order' => 2],
        ['key' => Project::STAGE_HOLD_BILLING, 'name' => 'Hold & Billing', 'sort_order' => 3],
        ['key' => Project::STAGE_PROGRESS, 'name' => 'Project Progress', 'sort_order' => 4],
        ['key' => Project::STAGE_HANDOVER, 'name' => 'Project Handover', 'sort_order' => 5],
    ];

    public static function ensureDefaultsForTenant(?int $tenantId): void
    {
        if (! $tenantId) {
            return;
        }

        $exists = self::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->exists();

        if ($exists) {
            return;
        }

        foreach (self::DEFAULT_STAGES as $stage) {
            self::withoutGlobalScopes()->create([
                'tenant_id' => $tenantId,
                'name' => $stage['name'],
                'key' => $stage['key'],
                'sort_order' => $stage['sort_order'],
                'is_active' => true,
                'is_default' => true,
            ]);
        }
    }

    public static function keysForTenant(?int $tenantId): array
    {
        if (! $tenantId) {
            return array_keys(Project::STAGES);
        }

        $keys = self::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->pluck('key')
            ->toArray();

        return empty($keys) ? array_keys(Project::STAGES) : $keys;
    }
}
