<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_approve_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
        ]);

        $response = $this->actingAs($superAdmin)
            ->patch(route('superadmin.tenants.update', $tenant, absolute: false), [
                'status' => Tenant::STATUS_APPROVED,
                'approval_notes' => 'Approved for onboarding.',
            ]);

        $response->assertRedirect(route('superadmin.tenants.show', $tenant, absolute: false));

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'status' => Tenant::STATUS_APPROVED,
            'approved_by' => $superAdmin->id,
        ]);
    }
}
