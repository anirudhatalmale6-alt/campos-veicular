@extends('layouts.app')
@section('title', $bank->exists ? 'Editar banco' : 'Novo banco/financeira')

@section('content')
@php $input = 'w-full border-slate-300 rounded-lg text-sm'; $lbl = 'block text-xs font-medium text-slate-500 mb-1'; @endphp

<form method="POST" action="{{ $bank->exists ? route('banks.update', $bank) : route('banks.store') }}"
      class="bg-white rounded-xl border border-slate-200 p-6 max-w-2xl">
    @csrf
    @if ($bank->exists) @method('PUT') @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="{{ $lbl }}">Nome *</label>
            <input type="text" name="name" value="{{ old('name', $bank->name) }}" required class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $lbl }}">Desconto estimado para quitação (%)</label>
            <input type="number" step="0.01" min="0" max="100" name="payoff_discount_percent"
                   value="{{ old('payoff_discount_percent', $bank->payoff_discount_percent) }}" class="{{ $input }} text-right">
        </div>
        <div>
            <label class="{{ $lbl }}">Contato</label>
            <input type="text" name="contact" value="{{ old('contact', $bank->contact) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $lbl }}">Telefone</label>
            <input type="text" name="phone" value="{{ old('phone', $bank->phone) }}" class="{{ $input }}">
        </div>
        <div class="flex items-end">
            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $bank->exists ? $bank->is_active : true)) class="rounded border-slate-300">
                Ativo
            </label>
        </div>
        <div class="md:col-span-2">
            <label class="{{ $lbl }}">Observações</label>
            <textarea name="notes" rows="3" class="{{ $input }}">{{ old('notes', $bank->notes) }}</textarea>
        </div>
    </div>

    <div class="mt-6 flex gap-2">
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-5 py-2 rounded-lg">Salvar</button>
        <a href="{{ route('banks.index') }}" class="text-sm text-slate-500 px-4 py-2">Cancelar</a>
    </div>
</form>
@endsection
