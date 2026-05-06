<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'holding_company_name' => config('app.name', 'Holding Company'),
            'company_name' => fake()->company(),
            'industry' => fake()->randomElement(['Technology', 'Manufacturing', 'Services']),
            'pic_name' => fake()->name(),
            'pic_email' => fake()->unique()->safeEmail(),
            'pic_phone' => fake()->phoneNumber(),
            'pic_job_title' => 'Company Admin',
            'estimated_users' => fake()->numberBetween(5, 200),
            'status' => Tenant::STATUS_PENDING,
        ];
    }
}
