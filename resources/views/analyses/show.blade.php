@extends('layouts.app')
@section('title', 'Análise ' . $analysis->code)

@section('content')
@php $row = 'flex justify-between py-1.5 border-b border-slate-50 text-sm'; @endphp

<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div>
        <h1 class="text-lg font-bold text-slate-800">{{ $analysis->code }} — {{ $analysis->vehicleTitle() }}</h1>
        <div class="text-sm text-slate-500">{{ $analysis->client?->name ?? 'Sem cliente' }} · {{ $analysis->statusLabel() }}</div>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('analyses.pdf', $analysis) }}" class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-4 py-2 rounded-lg">📄 Relatório PDF</a>
        @can('analyses.manage')
            <a href="{{ route('analyses.edit', $analysis) }}" class="bg-white border border-slate-300 hover:bg-slate-50 text-sm px-4 py-2 rounded-lg">Editar</a>
            @if (! $analysis->converted_vehicle_id)
                <form method="POST" action="{{ route('analyses.convert', $analysis) }}"
                      onsubmit="return confirm('Converter esta análise em veículo da frota?')">
                    @csrf
                    <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">➜ Converter em frota</button>
                </form>
            @else
                <span class="inline-flex items-center bg-emerald-100 text-emerald-700 text-sm px-4 py-2 rounded-lg">✓ Convertida (veículo #{{ $analysis->converted_vehicle_id }})</span>
            @endif
        @endcan
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Resultado --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-slate-700">Resultado da análise</h2>
                @if ($analysis->is_viable)
                    <span class="bg-emerald-100 text-emerald-700 text-sm font-semibold px-3 py-1 rounded-full">✓ Negócio viável</span>
                @else
                    <span class="bg-red-100 text-red-700 text-sm font-semibold px-3 py-1 rounded-full">✗ Não viável</span>
                @endif
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @php
                    $metrics = [
                        ['Custo total da aquisição', brl($analysis->total_acquisition_cost)],
                        ['Valor FIPE', brl($analysis->fipe_value)],
                        ['Percentual sobre FIPE', pct($analysis->fipe_percent)],
                        ['Margem estimada (R$)', brl($analysis->estimated_margin_value)],
                        ['Margem estimada (%)', pct($analysis->estimated_margin_percent)],
                        ['Valor máx. recomendado', brl($analysis->recommended_max_proposal)],
                    ];
                @endphp
                @foreach ($metrics as [$label, $value])
                    <div class="bg-slate-50 rounded-lg p-3">
                        <div class="text-xs text-slate-500">{{ $label }}</div>
                        <div class="font-semibold text-slate-800 mt-1">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Detalhamento --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h2 class="font-semibold text-slate-700 mb-3">Veículo</h2>
                <div class="{{ $row }}"><span class="text-slate-500">Marca/Modelo</span><span>{{ $analysis->vehicleTitle() }}</span></div>
                <div class="{{ $row }}"><span class="text-slate-500">Placa</span><span>{{ $analysis->vehicle_plate ?: '—' }}</span></div>
                <div class="{{ $row }}"><span class="text-slate-500">Cor</span><span>{{ $analysis->vehicle_color ?: '—' }}</span></div>
                <div class="{{ $row }}"><span class="text-slate-500">KM</span><span>{{ $analysis->vehicle_km ? number_format($analysis->vehicle_km, 0, ',', '.') : '—' }}</span></div>
                <div class="{{ $row }}"><span class="text-slate-500">Renavam</span><span>{{ $analysis->vehicle_renavam ?: '—' }}</span></div>
                <div class="{{ $row }}"><span class="text-slate-500">Banco</span><span>{{ $analysis->bank?->name ?? '—' }}</span></div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h2 class="font-semibold text-slate-700 mb-3">Financiamento &amp; custos</h2>
                <div class="{{ $row }}"><span class="text-slate-500">Parcelas (pg/atr/rest)</span><span>{{ $analysis->installments_paid }}/{{ $analysis->installments_late }}/{{ $analysis->installments_remaining }}</span></div>
                <div class="{{ $row }}"><span class="text-slate-500">Valor da parcela</span><span>{{ brl($analysis->installment_value) }}</span></div>
                <div class="{{ $row }}"><span class="text-slate-500">Dívida bruta</span><span>{{ brl($analysis->gross_debt) }}</span></div>
                <div class="{{ $row }}"><span class="text-slate-500">Quitação</span><span>{{ brl($analysis->payoff_value) }}</span></div>
                <div class="{{ $row }}"><span class="text-slate-500">Multas / IPVA</span><span>{{ brl($analysis->fines) }} / {{ brl($analysis->ipva) }}</span></div>
                <div class="{{ $row }}"><span class="text-slate-500">Solicitado / Comissão</span><span>{{ brl($analysis->client_requested_amount) }} / {{ brl($analysis->commission) }}</span></div>
                <div class="{{ $row }}"><span class="text-slate-500">Transporte / Manut.</span><span>{{ brl($analysis->transport) }} / {{ brl($analysis->estimated_maintenance) }}</span></div>
                <div class="{{ $row }}"><span class="text-slate-500">Demais custos</span><span>{{ brl($analysis->other_costs) }}</span></div>
            </div>
        </div>

        @if ($analysis->observations)
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h2 class="font-semibold text-slate-700 mb-2">Observações</h2>
                <p class="text-sm text-slate-600 whitespace-pre-line">{{ $analysis->observations }}</p>
            </div>
        @endif

        {{-- Anexos --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-semibold text-slate-700 mb-3">Anexos, fotos e documentos</h2>
            @can('analyses.manage')
                <form method="POST" action="{{ route('analyses.attachments.store', $analysis) }}" enctype="multipart/form-data"
                      class="flex flex-wrap items-end gap-3 mb-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Tipo</label>
                        <select name="type" class="border-slate-300 rounded-lg text-sm">
                            <option value="photo">Foto</option>
                            <option value="document">Documento</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Arquivo (máx. 10MB)</label>
                        <input type="file" name="file" required class="text-sm">
                    </div>
                    <button class="bg-slate-800 text-white text-sm px-4 py-2 rounded-lg">Enviar</button>
                </form>
            @endcan

            @if ($analysis->attachments->isEmpty())
                <p class="text-sm text-slate-400">Nenhum anexo ainda.</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach ($analysis->attachments as $att)
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            @if ($att->isImage())
                                <a href="{{ Storage::url($att->path) }}" target="_blank">
                                    <img src="{{ Storage::url($att->path) }}" class="w-full h-24 object-cover">
                                </a>
                            @else
                                <a href="{{ Storage::url($att->path) }}" target="_blank" class="flex items-center justify-center h-24 bg-slate-50 text-3xl">📄</a>
                            @endif
                            <div class="p-2">
                                <div class="text-xs text-slate-600 truncate" title="{{ $att->original_name }}">{{ $att->original_name }}</div>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-[10px] text-slate-400">{{ ucfirst($att->type) }}</span>
                                    @can('analyses.manage')
                                        <form method="POST" action="{{ route('analyses.attachments.destroy', [$analysis, $att]) }}" onsubmit="return confirm('Remover anexo?')">
                                            @csrf @method('DELETE')
                                            <button class="text-[10px] text-red-500 hover:underline">excluir</button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Lateral: responsavel + historico/observacoes --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5 text-sm">
            <h2 class="font-semibold text-slate-700 mb-3">Informações</h2>
            <div class="{{ $row }}"><span class="text-slate-500">Responsável</span><span>{{ $analysis->responsible?->name ?? '—' }}</span></div>
            <div class="{{ $row }}"><span class="text-slate-500">Margem alvo</span><span>{{ pct($analysis->target_margin_percent) }}</span></div>
            <div class="{{ $row }}"><span class="text-slate-500">Criada em</span><span>{{ $analysis->created_at->format('d/m/Y H:i') }}</span></div>
            <div class="{{ $row }}"><span class="text-slate-500">Atualizada</span><span>{{ $analysis->updated_at->format('d/m/Y H:i') }}</span></div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-semibold text-slate-700 mb-3">Histórico &amp; observações</h2>
            @can('analyses.manage')
                <form method="POST" action="{{ route('analyses.notes.store', $analysis) }}" class="mb-4">
                    @csrf
                    <textarea name="body" rows="2" required placeholder="Adicionar observação..."
                              class="w-full border-slate-300 rounded-lg text-sm mb-2"></textarea>
                    <button class="bg-emerald-600 text-white text-sm px-4 py-1.5 rounded-lg">Adicionar</button>
                </form>
            @endcan

            <ol class="space-y-3">
                @forelse ($analysis->notes as $note)
                    <li class="text-sm border-l-2 border-slate-200 pl-3">
                        <div class="text-slate-700">
                            @if ($note->type === 'status_change')
                                <span class="text-emerald-600 font-medium">Status alterado</span>
                                @if ($note->old_status) de <em>{{ \App\Models\Analysis::STATUSES[$note->old_status] ?? $note->old_status }}</em> @endif
                                para <em>{{ \App\Models\Analysis::STATUSES[$note->new_status] ?? $note->new_status }}</em>.
                            @endif
                            @if ($note->body) <span>{{ $note->body }}</span> @endif
                        </div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $note->user?->name ?? 'Sistema' }} · {{ $note->created_at->format('d/m/Y H:i') }}</div>
                    </li>
                @empty
                    <li class="text-sm text-slate-400">Nenhuma movimentação registrada.</li>
                @endforelse
            </ol>
        </div>

        @can('analyses.manage')
            <form method="POST" action="{{ route('analyses.destroy', $analysis) }}" onsubmit="return confirm('Excluir esta análise? Esta ação não pode ser desfeita.')">
                @csrf @method('DELETE')
                <button class="w-full text-sm text-red-600 hover:text-red-800 py-2">Excluir análise</button>
            </form>
        @endcan
    </div>
</div>
@endsection
