@extends('layouts.app')
@section('title', 'Painel')

@section('content')
    @php
        $card = 'bg-white rounded-xl border border-slate-200 p-5';
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="{{ $card }}">
            <div class="text-xs text-slate-500">Análises no total</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['analyses_total'] }}</div>
            <div class="text-xs text-emerald-600 mt-1">{{ $stats['analyses_viable'] }} viáveis</div>
        </div>
        <div class="{{ $card }}">
            <div class="text-xs text-slate-500">Análises em aberto</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['analyses_open'] }}</div>
            <div class="text-xs text-slate-400 mt-1">em análise / negociação</div>
        </div>
        <div class="{{ $card }}">
            <div class="text-xs text-slate-500">Veículos na frota</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['fleet_total'] }}</div>
            <div class="text-xs text-slate-400 mt-1">{{ $stats['fleet_available'] }} disp. · {{ $stats['fleet_rented'] }} alug.</div>
        </div>
        <div class="{{ $card }}">
            <div class="text-xs text-slate-500">Investido em aquisições</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ brl($stats['invested']) }}</div>
            <div class="text-xs text-slate-400 mt-1">{{ $stats['clients_total'] }} clientes · {{ $stats['banks_total'] }} bancos</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-semibold text-slate-700">Análises recentes</h2>
                @can('analyses.view')
                    <a href="{{ route('analyses.index') }}" class="text-sm text-emerald-600 hover:underline">Ver todas</a>
                @endcan
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-2 font-medium">Código</th>
                            <th class="px-5 py-2 font-medium">Veículo</th>
                            <th class="px-5 py-2 font-medium">Custo total</th>
                            <th class="px-5 py-2 font-medium">Status</th>
                            <th class="px-5 py-2 font-medium">Viável</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentAnalyses as $a)
                            <tr class="border-b border-slate-50 hover:bg-slate-50">
                                <td class="px-5 py-2">
                                    <a href="{{ route('analyses.show', $a) }}" class="text-emerald-600 hover:underline">{{ $a->code }}</a>
                                </td>
                                <td class="px-5 py-2">{{ $a->vehicleTitle() }}</td>
                                <td class="px-5 py-2">{{ brl($a->total_acquisition_cost) }}</td>
                                <td class="px-5 py-2">{{ $a->statusLabel() }}</td>
                                <td class="px-5 py-2">
                                    @if ($a->is_viable)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">Sim</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">Não</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-6 text-center text-slate-400">Nenhuma análise cadastrada ainda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-semibold text-slate-700 mb-4">Análises por status</h2>
            <div class="space-y-3">
                @foreach (\App\Models\Analysis::STATUSES as $key => $label)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-600">{{ $label }}</span>
                            <span class="font-semibold text-slate-800">{{ $analysesByStatus[$key] ?? 0 }}</span>
                        </div>
                        @php
                            $total = max($stats['analyses_total'], 1);
                            $w = round((($analysesByStatus[$key] ?? 0) / $total) * 100);
                        @endphp
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500" style="width: {{ $w }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
