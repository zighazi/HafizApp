<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRoleRequest;

class UserAdminController extends Controller
{
    /**
     * List user dengan pencarian & pagination.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $role = $request->get('role');

        $users = User::query()
            ->when($q !== '', function ($qry) use ($q) {
                $qry->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($role, fn($qry) => $qry->where('role', $role))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q', 'role'));
    }

    /**
     * Form edit role user.
     */
    public function edit(User $user)
    {
        // Lindungi agar admin tidak mengubah dirinya menjadi non-admin tanpa sengaja (opsional)
        $roles = ['admin' => 'Admin', 'orangtua' => 'Orangtua', 'guru' => 'Guru', 'user' => 'User'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update role user.
     */
    public function update(UpdateUserRoleRequest $request, User $user)
    {
        $user->update(['role' => $request->role]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Role untuk {$user->name} diperbarui menjadi {$request->role}.");
    }
}