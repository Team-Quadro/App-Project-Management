<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($model instanceof User) {
            return;
        }

        if (app()->runningInConsole()) {
            return;
        }

        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            if (session()->has('superadmin_tenant_id')) {
                $builder->where($model->getTable() . '.tenant_id', session('superadmin_tenant_id'));
            }
            return;
        }

        if ($user->tenant_id) {
            $builder->where($model->getTable() . '.tenant_id', $user->tenant_id);
        }
    }
}
