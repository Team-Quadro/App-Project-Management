<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model): void {
            $hasTenantAttribute = in_array('tenant_id', $model->getFillable(), true)
                || array_key_exists('tenant_id', $model->getAttributes());

            if (! $hasTenantAttribute) {
                return;
            }

            if (! is_null($model->tenant_id)) {
                return;
            }

            $tenantId = auth()->user()?->tenant_id;

            if ($tenantId) {
                $model->tenant_id = $tenantId;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
