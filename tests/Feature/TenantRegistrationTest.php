<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_registration_creates_pending_tenant(): void
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->post(route('tenants.store', absolute: false), [
            'company_name' => 'Subsidiary A',
            'industry' => 'Technology',
            'pic_phone' => '+62-111',
            'pic_job_title' => 'Company Admin',
            'estimated_users' => 15,
        ]);

        $response->assertRedirect(route('onboarding.index', absolute: false));
        $this->assertDatabaseHas('tenants', [
            'company_name' => 'Subsidiary A',
            'status' => Tenant::STATUS_PENDING,
            'pic_email' => $user->email,
            'requested_by' => $user->id,
        ]);
    }
}
