<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_tenant_is_logged_out(): void
    {
        $tenant = Tenant::factory()->create([
            'status' => Tenant::STATUS_DISABLED,
        ]);

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'member',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('login', absolute: false));
        $this->assertGuest();
    }
}
