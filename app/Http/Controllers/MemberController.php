<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    /**
     * Tampilkan daftar member dalam tenant yang sama (opsional, jika butuh halamannya)
     */
    public function index(Request $request)
    {
        // Mengambil member yang satu tenant dengan admin
        $members = User::where('tenant_id', $request->user()->tenant_id)
            ->where('role', User::ROLE_MEMBER)
            ->latest()
            ->paginate(10);

        return view('members.index', compact('members'));
    }

    /**
     * Tampilkan form pembuatan member
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Simpan member baru ke database
     */
    public function store(StoreMemberRequest $request)
    {
        $admin = $request->user();
        
        // Buat password default (bisa diubah nanti oleh member di fitur profile)
        $defaultPassword = 'password123'; 

        $member = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'job_title'       => $request->job_title,
            'password'        => Hash::make($defaultPassword),
            
            // Core Logic RBAC & Multi-Tenant
            'role'            => User::ROLE_MEMBER,
            'tenant_id'       => $admin->tenant_id,
            'approval_status' => 'approved', // Langsung approved, tidak perlu lewat Superadmin
        ]);

        return redirect()
            ->route('members.index')
            ->with('success', "Member {$member->name} berhasil ditambahkan dengan password default: {$defaultPassword}");
    }
    
    /**
     * Hapus member (Opsional, untuk melengkapi fitur)
     */
    public function destroy(User $member, Request $request)
    {
        // Pastikan admin hanya bisa menghapus member dari tenant-nya sendiri
        abort_if($member->tenant_id !== $request->user()->tenant_id, 403, 'Unauthorized action.');
        
        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('success', 'Member berhasil dihapus.');
    }
}