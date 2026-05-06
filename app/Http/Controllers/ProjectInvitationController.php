<?php

namespace App\Http\Controllers;

use App\Models\ProjectInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectInvitationController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        $invitations = ProjectInvitation::with('project.owner')
            ->where('email', $user->email)
            ->where('status', ProjectInvitation::STATUS_PENDING)
            ->latest()
            ->get();

        return view('invitations.index', compact('invitations'));
    }

    public function accept(ProjectInvitation $invitation): RedirectResponse
    {
        $user = request()->user();

        if ($invitation->email !== $user->email) {
            abort(403);
        }

        if ($invitation->status !== ProjectInvitation::STATUS_PENDING) {
            return redirect()
                ->route('invitations.index')
                ->with('error', 'Invitation has already been processed.');
        }

        $invitation->project->members()->syncWithoutDetaching([
            $user->id => ['tenant_id' => $invitation->tenant_id],
        ]);

        $invitation->update([
            'status' => ProjectInvitation::STATUS_ACCEPTED,
            'accepted_user_id' => $user->id,
            'responded_at' => now(),
        ]);

        return redirect()
            ->route('projects.show', $invitation->project)
            ->with('success', 'You have joined the project.');
    }

    public function decline(ProjectInvitation $invitation): RedirectResponse
    {
        $user = request()->user();

        if ($invitation->email !== $user->email) {
            abort(403);
        }

        if ($invitation->status !== ProjectInvitation::STATUS_PENDING) {
            return redirect()
                ->route('invitations.index')
                ->with('error', 'Invitation has already been processed.');
        }

        $invitation->update([
            'status' => ProjectInvitation::STATUS_DECLINED,
            'responded_at' => now(),
        ]);

        return redirect()
            ->route('invitations.index')
            ->with('success', 'Invitation declined.');
    }
}
