@extends('layouts.app')
@section('title', $user->exists ? 'Editar usuário' : 'Novo usuário')

@section('content')
@php $input = 'w-full border-slate-300 rounded-lg text-sm'; $lbl = 'block text-xs font-medium text-slate-500 mb-1'; $current = $user->getRoleNames()->first(); @endphp

<form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}"
      class="bg-white rounded-xl border border-slate-200 p-6 max-w-2xl">
    @csrf
    @if ($user->exists) @method('PUT') @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="{{ $lbl }}">Nome *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $lbl }}">E-mail *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $lbl }}">Telefone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $lbl }}">Perfil de acesso *</label>
            <select name="role" class="{{ $input }}" required>
                @foreach ($roles as $key => $label)
                    <option value="{{ $key }}" @selected(old('role', $current) === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $lbl }}">Senha {{ $user->exists ? '(deixe em branco para manter)' : '*' }}</label>
            <input type="password" name="password" class="{{ $input }}" {{ $user->exists ? '' : 'required' }}>
        </div>
        <div class="flex items-end">
            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->exists ? $user->is_active : true)) class="rounded border-slate-300">
                Usuário ativo
            </label>
        </div>
    </div>

    <div class="mt-6 flex gap-2">
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-5 py-2 rounded-lg">Salvar</button>
        <a href="{{ route('users.index') }}" class="text-sm text-slate-500 px-4 py-2">Cancelar</a>
    </div>
</form>
@endsection
