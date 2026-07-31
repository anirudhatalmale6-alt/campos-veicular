@extends('layouts.app')
@section('title', 'Bancos & Financeiras')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div class="text-sm text-slate-500">{{ $banks->total() }} registro(s)</div>
        @can('banks.manage')
            <a href="{{ route('banks.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">+ Novo banco/financeira</a>
        @endcan
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-slate-500 border-b border-slate-100 bg-slate-50">
                <tr>
                    <th class="px-4 py-3 font-medium">Nome</th>
                    <th class="px-4 py-3 font-medium">Desconto p/ quitação</th>
                    <th class="px-4 py-3 font-medium">Contato</th>
                    <th class="px-4 py-3 font-medium">Situação</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($banks as $bank)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $bank->name }}</td>
                        <td class="px-4 py-3">{{ pct($bank->payoff_discount_percent) }}</td>
                        <td class="px-4 py-3">{{ $bank->contact ?: '—' }} {{ $bank->phone ? '· '.$bank->phone : '' }}</td>
                        <td class="px-4 py-3">
                            @if ($bank->is_active)
                                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Ativo</span>
                            @else
                                <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">Inativo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @can('banks.manage')
                                <a href="{{ route('banks.edit', $bank) }}" class="text-emerald-600 hover:underline">Editar</a>
                                <form method="POST" action="{{ route('banks.destroy', $bank) }}" class="inline" onsubmit="return confirm('Remover?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:underline ml-2">Excluir</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Nenhum banco cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $banks->links() }}</div>
@endsection
