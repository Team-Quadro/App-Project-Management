<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyRole extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public const DEFAULT_ROLES = [
        'Front End',
        'Back End',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'company_role_id');
    }

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

        foreach (self::DEFAULT_ROLES as $roleName) {
            self::withoutGlobalScopes()->create([
                'tenant_id' => $tenantId,
                'name' => $roleName,
                'is_default' => true,
            ]);
        }
    }
}
