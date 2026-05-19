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

        // Superadmin 1
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('superadmin123'),
            'role' => 'superadmin',
            'is_active' => true,
            'approval_status' => 'approved',
            'job_title' => 'System Administrator',
        ]);

        // Superadmin 2
        User::create([
            'name' => 'Super Admin 2',
            'email' => 'superadmin2@example.com',
            'password' => bcrypt('superadmin321'),
            'role' => 'superadmin',
            'is_active' => true,
            'approval_status' => 'approved',
            'job_title' => 'System Administrator',
        ]);

        // Admin Perusahaan
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => \App\Models\User::ROLE_PIC,
            'tenant_id' => $tenant->id,
            'is_active' => true,
            'approval_status' => 'approved',
            'job_title' => 'Project Manager',
        ]);

        $tenant->update([
            'requested_by' => $admin->id,
            'approved_by' => $admin->id,
        ]);

        // Member 1
        $joko = User::create([
            'name' => 'Joko Wi',
            'email' => 'jokogemink@example.com',
            'password' => bcrypt('user123'),
            'role' => 'member',
            'tenant_id' => $tenant->id,
            'is_active' => true,
            'approval_status' => 'approved',
            'job_title' => 'Frontend Developer',
        ]);

        // Member 2
        $prabo = User::create([
            'name' => 'Prabo Wo',
            'email' => 'prabogemink@example.com',
            'password' => bcrypt('user123'),
            'role' => 'member',
            'tenant_id' => $tenant->id,
            'is_active' => true,
            'approval_status' => 'approved',
            'job_title' => 'Backend Developer',
        ]);

        // Member 3
        $gibrun = User::create([
            'name' => 'Gib Run',
            'email' => 'gibrun@example.com',
            'password' => bcrypt('user123'),
            'role' => 'member',
            'tenant_id' => $tenant->id,
            'is_active' => true,
            'approval_status' => 'approved',
            'job_title' => 'Mobile Developer',
        ]);

        $projectWebsite = Project::create([
            'tenant_id' => $tenant->id,
            'title' => 'Company Website Redesign',
            'description' => 'Complete overhaul of the company website including new branding, responsive design, and improved UX.',
            'status' => 'active',
            'stage' => 'project_progress',
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
            'stage' => 'proposal',
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
            'stage' => 'approach_lead_client',
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
            'stage' => 'project_handover',
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
            'stage' => 'hold_billing',
            'owner_id' => $gibrun->id,
            'deadline' => null,
        ]);

        // Create default global workflow stages for this tenant
        $stageTodo = \App\Models\WorkflowStage::create([
            'tenant_id'  => $tenant->id,
            'project_id' => null,
            'name'       => 'Todo',
            'key'        => 'todo',
            'color'      => '#6b7280',
            'sort_order' => 1,
        ]);
        $stageDoing = \App\Models\WorkflowStage::create([
            'tenant_id'  => $tenant->id,
            'project_id' => null,
            'name'       => 'Doing',
            'key'        => 'doing',
            'color'      => '#3b82f6',
            'sort_order' => 2,
        ]);
        $stageDone = \App\Models\WorkflowStage::create([
            'tenant_id'  => $tenant->id,
            'project_id' => null,
            'name'       => 'Done',
            'key'        => 'done',
            'color'      => '#22c55e',
            'sort_order' => 3,
        ]);

        $tenantStages = collect([$stageTodo, $stageDoing, $stageDone]);

        $this->createTasks($projectWebsite, $tenantStages, [
            ['title' => 'Design homepage mockup', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $joko->id, 'deadline' => now()->addWeeks(1)],
            ['title' => 'Implement responsive navbar', 'status' => 'doing', 'priority' => 'high', 'assigned_to' => $prabo->id, 'deadline' => now()->addWeeks(2)],
            ['title' => 'Create about page', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => $joko->id, 'deadline' => now()->addWeeks(3)],
            ['title' => 'Set up CI/CD pipeline', 'status' => 'todo', 'priority' => 'low', 'assigned_to' => null, 'deadline' => now()->addMonth()],
            ['title' => 'Write unit tests', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => $prabo->id, 'deadline' => now()->addMonth()],
        ]);

        $this->createTasks($projectMobile, $tenantStages, [
            ['title' => 'Set up React Native project', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $prabo->id, 'deadline' => now()->addWeeks(1)],
            ['title' => 'Design login screen', 'status' => 'doing', 'priority' => 'high', 'assigned_to' => $gibrun->id, 'deadline' => now()->addWeeks(2)],
            ['title' => 'Implement push notifications', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => null, 'deadline' => now()->addMonths(2)],
        ]);

        $this->createTasks($projectApi, $tenantStages, [
            ['title' => 'Research payment gateway options', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $prabo->id, 'deadline' => now()->subDays(3)],
            ['title' => 'Implement Stripe integration', 'status' => 'doing', 'priority' => 'high', 'assigned_to' => $joko->id, 'deadline' => now()->addWeeks(2)],
            ['title' => 'Write API documentation', 'status' => 'todo', 'priority' => 'low', 'assigned_to' => null, 'deadline' => now()->addMonth()],
        ]);

        $this->createTasks($projectLegacy, $tenantStages, [
            ['title' => 'Export legacy data', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $gibrun->id, 'deadline' => now()->subMonths(1)],
            ['title' => 'Import to new system', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $gibrun->id, 'deadline' => now()->subWeeks(2)],
            ['title' => 'Validate data', 'status' => 'done', 'priority' => 'medium', 'assigned_to' => $admin->id, 'deadline' => now()->subWeek()],
        ]);
    }

    /**
     * Helper to create tasks for a project using tenant-wide stages.
     */
    private function createTasks(Project $project, \Illuminate\Support\Collection $tenantStages, array $tasks): void
    {
        foreach ($tasks as $data) {
            $status = $data['status'] ?? Task::STATUS_TODO;
            $stage = $tenantStages->firstWhere('key', $status) ?? $tenantStages->first();

            Task::create(array_merge($data, [
                'tenant_id'  => $project->tenant_id,
                'project_id' => $project->id,
                'stage_id'   => $stage?->id,
            ]));
        }
    }
}