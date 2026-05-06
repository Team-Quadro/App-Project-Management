<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tenant = Tenant::create([
            'company_name' => 'Demo Subsidiary',
            'industry' => 'Technology',
            'pic_name' => 'PIC Demo',
            'pic_email' => 'pic@demo-subsidiary.test',
            'pic_phone' => '+62-000-000-0000',
            'pic_job_title' => 'Company Admin',
            'estimated_users' => 25,
            'status' => Tenant::STATUS_APPROVED,
            'approved_at' => now(),
        ]);

        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('superadmin123'),
            'role' => 'superadmin',
        ]);

        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => \App\Models\User::ROLE_PIC,
            'tenant_id' => $tenant->id,
        ]);

        $tenant->update([
            'requested_by' => $admin->id,
            'approved_by' => $admin->id,
        ]);

        $joko = User::create([
            'name' => 'Joko Wi',
            'email' => 'jokogemink@example.com',
            'password' => bcrypt('user123'),
            'role' => 'member',
            'tenant_id' => $tenant->id,
        ]);

        $prabo = User::create([
            'name' => 'Prabo Wo',
            'email' => 'prabogemink@example.com',
            'password' => bcrypt('user123'),
            'role' => 'member',
            'tenant_id' => $tenant->id,
        ]);

        $gibrun = User::create([
            'name' => 'Gib Run',
            'email' => 'gibrun@example.com',
            'password' => bcrypt('user123'),
            'role' => 'member',
            'tenant_id' => $tenant->id,
        ]);

        $projectWebsite = Project::create([
            'tenant_id' => $tenant->id,
            'title' => 'Company Website Redesign',
            'description' => 'Complete overhaul of the company website including new branding, responsive design, and improved UX.',
            'status' => 'active',
            'owner_id' => $admin->id,
            'deadline' => now()->addMonths(2),
        ]);
        $projectWebsite->members()->attach([
            $joko->id => ['tenant_id' => $tenant->id],
            $prabo->id => ['tenant_id' => $tenant->id],
        ]);

        $projectMobile = Project::create([
            'tenant_id' => $tenant->id,
            'title' => 'Mobile App Development',
            'description' => 'Build a cross-platform mobile application for customer self-service portal.',
            'status' => 'active',
            'owner_id' => $joko->id,
            'deadline' => now()->addMonths(3),
        ]);
        $projectMobile->members()->attach([
            $prabo->id => ['tenant_id' => $tenant->id],
            $gibrun->id => ['tenant_id' => $tenant->id],
        ]);

        $projectApi = Project::create([
            'tenant_id' => $tenant->id,
            'title' => 'API Integration',
            'description' => 'Integrate third-party payment and logistics APIs into the existing system.',
            'status' => 'active',
            'owner_id' => $prabo->id,
            'deadline' => now()->addMonth(),
        ]);
        $projectApi->members()->attach([
            $joko->id => ['tenant_id' => $tenant->id],
        ]);

        $projectLegacy = Project::create([
            'tenant_id' => $tenant->id,
            'title' => 'Legacy System Migration',
            'description' => 'Migrate data and features from the old PHP system to the new Laravel platform.',
            'status' => 'completed',
            'owner_id' => $admin->id,
            'deadline' => now()->subWeek(),
        ]);
        $projectLegacy->members()->attach([
            $gibrun->id => ['tenant_id' => $tenant->id],
        ]);

        $projectDocs = Project::create([
            'tenant_id' => $tenant->id,
            'title' => 'Internal Documentation',
            'description' => 'Create and maintain internal technical documentation and developer guides.',
            'status' => 'archived',
            'owner_id' => $gibrun->id,
            'deadline' => null,
        ]);

        $this->createTasks($projectWebsite, [
            ['title' => 'Design homepage mockup', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $joko->id, 'deadline' => now()->addWeeks(1)],
            ['title' => 'Implement responsive navbar', 'status' => 'in_progress', 'priority' => 'high', 'assigned_to' => $prabo->id, 'deadline' => now()->addWeeks(2)],
            ['title' => 'Create about page', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => $joko->id, 'deadline' => now()->addWeeks(3)],
            ['title' => 'Set up CI/CD pipeline', 'status' => 'todo', 'priority' => 'low', 'assigned_to' => null, 'deadline' => now()->addMonth()],
            ['title' => 'Write unit tests', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => $prabo->id, 'deadline' => now()->addMonth()],
        ]);

        $this->createTasks($projectMobile, [
            ['title' => 'Set up React Native project', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $prabo->id, 'deadline' => now()->addWeeks(1)],
            ['title' => 'Design login screen', 'status' => 'in_progress', 'priority' => 'high', 'assigned_to' => $gibrun->id, 'deadline' => now()->addWeeks(2)],
            ['title' => 'Implement push notifications', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => null, 'deadline' => now()->addMonths(2)],
        ]);

        $this->createTasks($projectApi, [
            ['title' => 'Research payment gateway options', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $prabo->id, 'deadline' => now()->subDays(3)],
            ['title' => 'Implement Stripe integration', 'status' => 'in_progress', 'priority' => 'high', 'assigned_to' => $joko->id, 'deadline' => now()->addWeeks(2)],
            ['title' => 'Write API documentation', 'status' => 'todo', 'priority' => 'low', 'assigned_to' => null, 'deadline' => now()->addMonth()],
        ]);

        $this->createTasks($projectLegacy, [
            ['title' => 'Export legacy data', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $gibrun->id, 'deadline' => now()->subMonths(1)],
            ['title' => 'Import to new system', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $gibrun->id, 'deadline' => now()->subWeeks(2)],
            ['title' => 'Validate data', 'status' => 'done', 'priority' => 'medium', 'assigned_to' => $admin->id, 'deadline' => now()->subWeek()],
        ]);
    }

    /**
     * Helper to create tasks for a project.
     */
    private function createTasks(Project $project, array $tasks): void
    {
        $project->loadMissing('stages');

        foreach ($tasks as $data) {
            $status = $data['status'] ?? Task::STATUS_TODO;
            $stage = $project->stages->firstWhere('key', $status) ?? $project->stages->first();

            Task::create(array_merge($data, [
                'tenant_id' => $project->tenant_id,
                'project_id' => $project->id,
                'stage_id' => $stage?->id,
            ]));
        }
    }
}