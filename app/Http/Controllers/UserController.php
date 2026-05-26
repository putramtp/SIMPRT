<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::with('roles')->latest();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('role', fn($u) => $u->roles->pluck('name')->join(', ') ?: '-')
                ->addColumn('action', function ($u) {
                    $html = '<a href="' . route('users.show', $u) . '" class="btn btn-sm btn-outline-secondary">Detail</a> ';
                    if (auth()->user()->can('edit users')) {
                        $html .= '<a href="' . route('users.edit', $u) . '" class="btn btn-sm btn-outline-primary">Edit</a> ';
                    }
                    if (auth()->user()->can('delete users')) {
                        $html .= '<form action="' . route('users.destroy', $u) . '" method="POST" class="d-inline"'
                            . ' onsubmit="return confirm(\'Hapus user ini?\')">'
                            . csrf_field() . method_field('DELETE')
                            . '<button class="btn btn-sm btn-outline-danger">Hapus</button></form>';
                    }
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('users.index');
    }

    public function create()
    {
        $roles = Role::whereNotIn('name', ['customer'])->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
        ]);
        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::whereNotIn('name', ['customer'])->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|exists:roles,name',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
