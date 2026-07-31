@extends('layouts.app')
@section('title', 'Usuários & Acessos')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div class="text-sm text-slate-500">{{ $users->total() }} usuário(s)</div>
        <a href="{{ route('users.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">+ Novo usuário</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-slate-500 border-b border-slate-100 bg-slate-50">
                <tr>
                    <th class="px-4 py-3 font-medium">Nome</th>
                    <th class="px-4 py-3 font-medium">E-mail</th>
                    <th class="px-4 py-3 font-medium">Perfil</th>
                    <th class="px-4 py-3 font-medium">Situação</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->roleLabel() ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($user->is_active)
                                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Ativo</span>
                            @else
                                <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">Inativo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('users.edit', $user) }}" class="text-emerald-600 hover:underline">Editar</a>
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Remover usuário?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:underline ml-2">Excluir</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
@endsection
