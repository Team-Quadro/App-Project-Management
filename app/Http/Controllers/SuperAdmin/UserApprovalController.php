<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserApprovalController extends Controller
{
    /**
     * Memproses approval atau penolakan user (karyawan).
     */
    public function process(Request $request, User $user)
    {
        // Validasi input dari form Super Admin
        $request->validate([
            'action'    => ['required', 'in:approve,reject'],
            // Role & Tenant wajib diisi KALAU action-nya 'approve'
            'role'      => ['required_if:action,approve', 'string', 'max:50', 'nullable'],
            'tenant_id' => ['required_if:action,approve', 'exists:tenants,id', 'nullable'],
        ]);

        // Skenario 1: Super Admin Menolak (Reject)
        if ($request->action === 'reject') {
            $user->update(['approval_status' => 'rejected']);

            return redirect()->back()->with('success', 'Akun karyawan berhasil ditolak.');
        }

        // Skenario 2: Super Admin Menerima (Approve)
        $user->update([
            'approval_status' => 'approved',
            'role'            => $request->role,        // Ketikan manual (misal: "UI/UX", "Developer")
            'tenant_id'       => $request->tenant_id,   // Dimasukkan ke perusahaan/workspace mana
        ]);

        return redirect()->back()->with('success', "Karyawan {$user->name} berhasil disetujui sebagai {$request->role}.");
    }
}
