@extends('layouts.app')
@section('title', 'Clientes')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ $search }}" placeholder="Buscar por nome ou documento"
                   class="border-slate-300 rounded-lg text-sm w-64">
            <button class="bg-slate-800 text-white text-sm px-4 rounded-lg">Buscar</button>
        </form>
        @can('clients.manage')
            <a href="{{ route('clients.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">+ Novo cliente</a>
        @endcan
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-slate-500 border-b border-slate-100 bg-slate-50">
                <tr>
                    <th class="px-4 py-3 font-medium">Nome</th>
                    <th class="px-4 py-3 font-medium">Documento</th>
                    <th class="px-4 py-3 font-medium">Telefone</th>
                    <th class="px-4 py-3 font-medium">Cidade/UF</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clients as $client)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $client->name }}</td>
                        <td class="px-4 py-3">{{ $client->document ?: '—' }}</td>
                        <td class="px-4 py-3">{{ $client->phone ?: '—' }}</td>
                        <td class="px-4 py-3">{{ trim($client->city . ($client->state ? '/'.$client->state : ''), '/') ?: '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            @can('clients.manage')
                                <a href="{{ route('clients.edit', $client) }}" class="text-emerald-600 hover:underline">Editar</a>
                                <form method="POST" action="{{ route('clients.destroy', $client) }}" class="inline" onsubmit="return confirm('Remover cliente?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:underline ml-2">Excluir</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Nenhum cliente cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $clients->links() }}</div>
@endsection
