<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        $roles = Role::all();
        return view('pages.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('pages.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8',
            'phone'     => 'nullable|string|max:20',
            'user_code' => 'nullable|string|max:50|unique:users',
            'roles'     => 'nullable|array',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'user_code' => $request->user_code,
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dibuat.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('pages.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password'  => 'nullable|string|min:8',
            'phone'     => 'nullable|string|max:20',
            'user_code' => 'nullable|string|max:50|unique:users,user_code,' . $user->id,
            'roles'     => 'nullable|array',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->user_code = $request->user_code;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
