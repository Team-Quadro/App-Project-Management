<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::with('tenant')->latest()->paginate(20);
        $tenants = Tenant::orderBy('company_name')->get();

        return view('superadmin.users.index', compact('users', 'tenants'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => $data['role'],
            'tenant_id' => $data['tenant_id'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        if ($user->role === User::ROLE_PIC && $user->tenant_id) {
            Tenant::where('id', $user->tenant_id)->update([
                'pic_user_id' => $user->id,
                'pic_name' => $user->name,
                'pic_email' => $user->email,
            ]);
        }

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        Tenant::where('pic_user_id', $user->id)->update([
            'pic_user_id' => null,
            'pic_name' => null,
            'pic_email' => null,
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'tenant_id' => $data['tenant_id'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        if ($user->role === User::ROLE_PIC && $user->tenant_id) {
            Tenant::where('id', $user->tenant_id)->update([
                'pic_user_id' => $user->id,
                'pic_name' => $user->name,
                'pic_email' => $user->email,
            ]);
        }

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
