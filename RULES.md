# AI Coding Rules & Project Standards

This document defines the coding standards, architectural guidelines, and principles for this Laravel + Livewire + Tailwind multi-tenant application. Always adhere to these rules when generating or modifying code.

## 1. Core Principles
*   **Modular and Readable:** Write code that is easy to understand. Use descriptive variable and method names. Break down large functions into smaller, single-responsibility methods.
*   **DRY (Don't Repeat Yourself):** Extract reusable logic into Traits, Services, Action classes, or Blade Components. Avoid duplicating code.
*   **KISS (Keep It Simple, Stupid):** Avoid over-engineering. Choose the simplest and most straightforward solution that meets the requirements.

## 2. Security First & Multi-Tenancy
*   **Strict Tenant Isolation:** Always apply `tenant_id` filtering. Use Laravel Global Scopes on all tenant-specific Eloquent models to ensure data from one subsidiary never leaks to another. 
*   **Never Trust User Input:** Always validate incoming request data using Laravel Form Requests or Livewire's `$rules` / `#[Validate]` attributes.
*   **Mass Assignment Protection:** Explicitly define `$fillable` or `$guarded` properties on all Eloquent models.
*   **Authorization:** Always check user permissions using Laravel Policies or Gates before performing CRUD operations, especially for cross-tenant or Admin-level actions.

## 3. Testing & Build Protocol
*   **Always Test Before Finishing:** Do not consider a task "done" without testing. Write PHPUnit or Pest tests for critical business logic (especially tenant isolation and workflow transitions).
*   **Asset Compilation:** Always ensure `npm run build` or `npm run dev` (Vite) runs successfully without errors when modifying Tailwind or JavaScript assets.
*   **Database Seeding:** Keep factories and seeders updated. Always test migrations by running `php artisan migrate:fresh --seed` locally to ensure a stable state.

## 4. Laravel Best Practices
*   **Skinny Controllers / Livewire Components:** Keep controllers and Livewire components strictly for handling HTTP requests/component state and returning responses/views.
*   **Fat Services / Actions:** Move complex business logic, particularly the dynamic pipeline/workflow logic, into dedicated Service classes or single-action classes (e.g., `MoveTaskToNextStageAction`).
*   **Database Optimization:** Strictly avoid N+1 query problems. Always use Eloquent's `with()` for eager loading relationships (e.g., loading `Task` with its `Assignee`).

## 5. Livewire & UI Interactivity
*   **Minimal Component State:** Do not pass large Eloquent collections or heavy objects to Livewire public properties if not strictly needed, as it bloats the network payload.
*   **Optimize Network Requests:** Use `wire:model.blur` or `wire:model.defer` for text inputs to prevent excessive server round-trips on every keystroke.
*   **Leverage Alpine.js:** For purely frontend UI states (like toggling dropdowns, modals, or basic DOM manipulation), use Alpine.js instead of sending a request to Livewire.

## 6. Tailwind CSS & Blade Views
*   **Component Extraction:** If a UI element (like a button, form input, or Kanban card) is used more than twice, extract it into an anonymous Blade Component (`<x-button>`).
*   **Utility-First:** Stick to Tailwind utility classes. Avoid writing custom CSS in `<style>` blocks unless absolutely necessary for complex animations or overrides.

## 7. Git & Commit Workflow
*   **Atomic Commits:** Make small, focused commits that represent a single logical change.
*   **Descriptive Messages:** Use conventional commit messages (e.g., `feat: add custom stage creation`, `fix: resolve tenant scope leak on tasks`, `refactor: extract Kanban card to blade component`).