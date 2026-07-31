<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', ['user' => new User(), 'roles' => Roles::LABELS]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(array_keys(Roles::LABELS))],
            'is_active' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => $request->boolean('is_active'),
        ]);
        $user->syncRoles([$data['role']]);

        return redirect()->route('users.index')->with('success', 'Usuário criado.');
    }

    public function edit(User $user)
    {
        return view('users.form', ['user' => $user, 'roles' => Roles::LABELS]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(array_keys(Roles::LABELS))],
            'is_active' => 'nullable|boolean',
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'is_active' => $request->boolean('is_active'),
            ...($data['password'] ? ['password' => Hash::make($data['password'])] : []),
        ]);
        $user->syncRoles([$data['role']]);

        return redirect()->route('users.index')->with('success', 'Usuário atualizado.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode remover o próprio usuário.');
        }
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuário removido.');
    }
}
