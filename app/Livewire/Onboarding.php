<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Tenant;
use App\Models\TenantJoinRequest;

#[Layout('layouts.onboarding')]
#[Title('Memulai')]
class Onboarding extends Component
{
    public function render()
    {
        $tenants = Tenant::orderBy('company_name')->get();

        $pendingRequest = TenantJoinRequest::withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->where('status', TenantJoinRequest::STATUS_PENDING)
            ->with('tenant')
            ->first();

        return view('livewire.onboarding', compact('tenants', 'pendingRequest'));
    }
}
