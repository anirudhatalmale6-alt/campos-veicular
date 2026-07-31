@extends('layouts.app')
@section('title', $analysis->exists ? 'Editar análise' : 'Nova análise')

@section('content')
@php
    $money = 'w-full border-slate-300 rounded-lg text-sm text-right';
    $input = 'w-full border-slate-300 rounded-lg text-sm';
    $labels = 'block text-xs font-medium text-slate-500 mb-1';
@endphp

<form method="POST"
      action="{{ $analysis->exists ? route('analyses.update', $analysis) : route('analyses.store') }}"
      x-data="analysisCalc({
          fipe: {{ (float) old('fipe_value', $analysis->fipe_value) }},
          payoff: {{ (float) old('payoff_value', $analysis->payoff_value) }},
          requested: {{ (float) old('client_requested_amount', $analysis->client_requested_amount) }},
          fines: {{ (float) old('fines', $analysis->fines) }},
          ipva: {{ (float) old('ipva', $analysis->ipva) }},
          commission: {{ (float) old('commission', $analysis->commission) }},
          transport: {{ (float) old('transport', $analysis->transport) }},
          maintenance: {{ (float) old('estimated_maintenance', $analysis->estimated_maintenance) }},
          other: {{ (float) old('other_costs', $analysis->other_costs) }},
          target: {{ (float) old('target_margin_percent', $analysis->target_margin_percent ?: 20) }}
      })">
    @csrf
    @if ($analysis->exists) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Coluna de entradas --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Identificacao --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h2 class="font-semibold text-slate-700 mb-4">Identificação</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="{{ $labels }}">Cliente</label>
                        <select name="client_id" class="{{ $input }}">
                            <option value="">— selecione —</option>
                            @foreach ($clients as $c)
                                <option value="{{ $c->id }}" @selected(old('client_id', $analysis->client_id) == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labels }}">Responsável</label>
                        <select name="responsible_user_id" class="{{ $input }}">
                            <option value="">— selecione —</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" @selected(old('responsible_user_id', $analysis->responsible_user_id) == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labels }}">Banco / Financeira</label>
                        <select name="bank_id" class="{{ $input }}">
                            <option value="">— selecione —</option>
                            @foreach ($banks as $b)
                                <option value="{{ $b->id }}" @selected(old('bank_id', $analysis->bank_id) == $b->id)>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labels }}">Status</label>
                        <select name="status" class="{{ $input }}">
                            @foreach ($statuses as $key => $label)
                                <option value="{{ $key }}" @selected(old('status', $analysis->status) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Veiculo --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h2 class="font-semibold text-slate-700 mb-4">Veículo</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ([
                        'vehicle_brand' => 'Marca',
                        'vehicle_model' => 'Modelo',
                        'vehicle_year' => 'Ano',
                        'vehicle_plate' => 'Placa',
                        'vehicle_color' => 'Cor',
                        'vehicle_renavam' => 'Renavam',
                    ] as $field => $label)
                        <div>
                            <label class="{{ $labels }}">{{ $label }}</label>
                            <input type="text" name="{{ $field }}" value="{{ old($field, $analysis->$field) }}" class="{{ $input }}">
                        </div>
                    @endforeach
                    <div>
                        <label class="{{ $labels }}">Quilometragem</label>
                        <input type="number" name="vehicle_km" value="{{ old('vehicle_km', $analysis->vehicle_km) }}" class="{{ $input }}">
                    </div>
                </div>
            </div>

            {{-- Financiamento --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h2 class="font-semibold text-slate-700 mb-4">Financiamento</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="{{ $labels }}">Parcelas pagas</label>
                        <input type="number" name="installments_paid" value="{{ old('installments_paid', $analysis->installments_paid) }}" class="{{ $input }}">
                    </div>
                    <div>
                        <label class="{{ $labels }}">Parcelas atrasadas</label>
                        <input type="number" name="installments_late" value="{{ old('installments_late', $analysis->installments_late) }}" class="{{ $input }}">
                    </div>
                    <div>
                        <label class="{{ $labels }}">Parcelas restantes</label>
                        <input type="number" name="installments_remaining" value="{{ old('installments_remaining', $analysis->installments_remaining) }}" class="{{ $input }}">
                    </div>
                    <div>
                        <label class="{{ $labels }}">Valor da parcela</label>
                        <input type="number" step="0.01" name="installment_value" value="{{ old('installment_value', $analysis->installment_value) }}" class="{{ $money }}">
                    </div>
                    <div>
                        <label class="{{ $labels }}">Dívida bruta</label>
                        <input type="number" step="0.01" name="gross_debt" value="{{ old('gross_debt', $analysis->gross_debt) }}" class="{{ $money }}">
                    </div>
                </div>
            </div>

            {{-- Custos --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h2 class="font-semibold text-slate-700 mb-4">Valores &amp; Custos</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="{{ $labels }}">Valor FIPE</label>
                        <input type="number" step="0.01" name="fipe_value" x-model.number="fipe" class="{{ $money }}">
                    </div>
                    <div>
                        <label class="{{ $labels }}">Quitação</label>
                        <input type="number" step="0.01" name="payoff_value" x-model.number="payoff" class="{{ $money }}">
                    </div>
                    <div>
                        <label class="{{ $labels }}">Valor solicitado pelo cliente</label>
                        <input type="number" step="0.01" name="client_requested_amount" x-model.number="requested" class="{{ $money }}">
                    </div>
                    <div>
                        <label class="{{ $labels }}">Multas</label>
                        <input type="number" step="0.01" name="fines" x-model.number="fines" class="{{ $money }}">
                    </div>
                    <div>
                        <label class="{{ $labels }}">IPVA</label>
                        <input type="number" step="0.01" name="ipva" x-model.number="ipva" class="{{ $money }}">
                    </div>
                    <div>
                        <label class="{{ $labels }}">Comissão</label>
                        <input type="number" step="0.01" name="commission" x-model.number="commission" class="{{ $money }}">
                    </div>
                    <div>
                        <label class="{{ $labels }}">Transporte</label>
                        <input type="number" step="0.01" name="transport" x-model.number="transport" class="{{ $money }}">
                    </div>
                    <div>
                        <label class="{{ $labels }}">Manutenção estimada</label>
                        <input type="number" step="0.01" name="estimated_maintenance" x-model.number="maintenance" class="{{ $money }}">
                    </div>
                    <div>
                        <label class="{{ $labels }}">Demais custos</label>
                        <input type="number" step="0.01" name="other_costs" x-model.number="other" class="{{ $money }}">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <label class="{{ $labels }}">Observações</label>
                <textarea name="observations" rows="3" class="{{ $input }}">{{ old('observations', $analysis->observations) }}</textarea>
            </div>
        </div>

        {{-- Coluna do resultado (calculo automatico) --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 p-5 sticky top-20">
                <h2 class="font-semibold text-slate-700 mb-4">Resultado da análise</h2>

                <div class="mb-4">
                    <label class="{{ $labels }}">Margem mínima desejada (%)</label>
                    <input type="number" step="0.01" name="target_margin_percent" x-model.number="target" class="{{ $money }}">
                </div>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Custo total da aquisição</dt>
                        <dd class="font-semibold text-slate-800" x-text="fmt(totalCost)"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Percentual sobre a FIPE</dt>
                        <dd class="font-semibold" :class="fipePercent <= 100 ? 'text-slate-800' : 'text-red-600'" x-text="pct(fipePercent)"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Margem estimada (R$)</dt>
                        <dd class="font-semibold" :class="marginValue >= 0 ? 'text-emerald-600' : 'text-red-600'" x-text="fmt(marginValue)"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Margem estimada (%)</dt>
                        <dd class="font-semibold" :class="marginPercent >= target ? 'text-emerald-600' : 'text-red-600'" x-text="pct(marginPercent)"></dd>
                    </div>
                    <div class="flex justify-between border-t border-slate-100 pt-3">
                        <dt class="text-slate-500">Valor máx. recomendado</dt>
                        <dd class="font-semibold text-slate-800" x-text="fmt(recommended)"></dd>
                    </div>
                </dl>

                <div class="mt-4 rounded-lg p-3 text-center font-semibold"
                     :class="viable ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'">
                    <span x-text="viable ? '✓ Negócio VIÁVEL' : '✗ Negócio NÃO viável'"></span>
                </div>

                <button class="mt-5 w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-lg text-sm font-medium">
                    {{ $analysis->exists ? 'Salvar alterações' : 'Criar análise' }}
                </button>
                <a href="{{ $analysis->exists ? route('analyses.show', $analysis) : route('analyses.index') }}"
                   class="mt-2 block text-center text-sm text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </div>
    </div>
</form>

<script>
    function analysisCalc(init) {
        return {
            ...init,
            get totalCost() {
                return this.payoff + this.requested + this.fines + this.ipva +
                    this.commission + this.transport + this.maintenance + this.other;
            },
            get fipePercent() { return this.fipe > 0 ? (this.totalCost / this.fipe) * 100 : 0; },
            get marginValue() { return this.fipe - this.totalCost; },
            get marginPercent() { return this.fipe > 0 ? (this.marginValue / this.fipe) * 100 : 0; },
            get recommended() { return this.fipe * (1 - this.target / 100); },
            get viable() { return this.fipe > 0 && this.marginPercent >= this.target; },
            fmt(v) { return 'R$ ' + (v || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}); },
            pct(v) { return (v || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '%'; },
        };
    }
</script>
@endsection
