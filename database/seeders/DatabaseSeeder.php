<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        $joko = User::create([
            'name' => 'Joko Wi',
            'email' => 'jokogemink@example.com',
            'password' => bcrypt('user123'),
            'role' => 'member',
        ]);

        $prabo = User::create([
            'name' => 'Prabo Wo',
            'email' => 'prabogemink@example.com',
            'password' => bcrypt('user123'),
            'role' => 'member',
        ]);

        $gibrun = User::create([
            'name' => 'Gib Run',
            'email' => 'gibrun@example.com',
            'password' => bcrypt('user123'),
            'role' => 'member',
        ]);

        $projectWebsite = Project::create([
            'title' => 'Company Website Redesign',
            'description' => 'Complete overhaul of the company website including new branding, responsive design, and improved UX.',
            'status' => 'active',
            'owner_id' => $admin->id,
            'deadline' => now()->addMonths(2),
        ]);
        $projectWebsite->members()->attach([$joko->id, $prabo->id]);

        $projectMobile = Project::create([
            'title' => 'Mobile App Development',
            'description' => 'Build a cross-platform mobile application for customer self-service portal.',
            'status' => 'active',
            'owner_id' => $joko->id,
            'deadline' => now()->addMonths(3),
        ]);
        $projectMobile->members()->attach([$prabo->id, $gibrun->id]);

        $projectApi = Project::create([
            'title' => 'API Integration',
            'description' => 'Integrate third-party payment and logistics APIs into the existing system.',
            'status' => 'active',
            'owner_id' => $prabo->id,
            'deadline' => now()->addMonth(),
        ]);
        $projectApi->members()->attach([$joko->id]);

        $projectLegacy = Project::create([
            'title' => 'Legacy System Migration',
            'description' => 'Migrate data and features from the old PHP system to the new Laravel platform.',
            'status' => 'completed',
            'owner_id' => $admin->id,
            'deadline' => now()->subWeek(),
        ]);
        $projectLegacy->members()->attach([$gibrun->id]);

        $projectDocs = Project::create([
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
        foreach ($tasks as $data) {
            Task::create(array_merge($data, ['project_id' => $project->id]));
        }
    }
}