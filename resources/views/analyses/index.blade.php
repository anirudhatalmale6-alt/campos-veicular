@extends('layouts.app')
@section('title', 'Análises & Aquisição')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div class="text-sm text-slate-500">{{ $analyses->total() }} análise(s)</div>
        @can('analyses.manage')
            <a href="{{ route('analyses.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">+ Nova análise</a>
        @endcan
    </div>

    <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" name="q" value="{{ $filters['search'] }}" placeholder="Código, marca, modelo, placa..."
               class="border-slate-300 rounded-lg text-sm md:col-span-2">
        <select name="status" class="border-slate-300 rounded-lg text-sm">
            <option value="">Todos os status</option>
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <select name="viable" class="border-slate-300 rounded-lg text-sm flex-1">
                <option value="">Viabilidade</option>
                <option value="1" @selected($filters['viable'] === '1')>Viáveis</option>
                <option value="0" @selected($filters['viable'] === '0')>Não viáveis</option>
            </select>
            <button class="bg-slate-800 text-white text-sm px-4 rounded-lg">Filtrar</button>
        </div>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-slate-500 border-b border-slate-100 bg-slate-50">
                <tr>
                    <th class="px-4 py-3 font-medium">Código</th>
                    <th class="px-4 py-3 font-medium">Cliente / Veículo</th>
                    <th class="px-4 py-3 font-medium">FIPE</th>
                    <th class="px-4 py-3 font-medium">Custo total</th>
                    <th class="px-4 py-3 font-medium">% FIPE</th>
                    <th class="px-4 py-3 font-medium">Margem</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Viável</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($analyses as $a)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $a->code }}</td>
                        <td class="px-4 py-3">
                            <div class="text-slate-800">{{ $a->client?->name ?? '—' }}</div>
                            <div class="text-xs text-slate-400">{{ $a->vehicleTitle() }}</div>
                        </td>
                        <td class="px-4 py-3">{{ brl($a->fipe_value) }}</td>
                        <td class="px-4 py-3">{{ brl($a->total_acquisition_cost) }}</td>
                        <td class="px-4 py-3">{{ pct($a->fipe_percent) }}</td>
                        <td class="px-4 py-3">{{ pct($a->estimated_margin_percent) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-700">{{ $a->statusLabel() }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($a->is_viable)
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">Sim</span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">Não</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('analyses.show', $a) }}" class="text-emerald-600 hover:underline">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">Nenhuma análise encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $analyses->links() }}</div>
@endsection
