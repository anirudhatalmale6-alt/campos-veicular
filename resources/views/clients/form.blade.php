@extends('layouts.app')
@section('title', $client->exists ? 'Editar cliente' : 'Novo cliente')

@section('content')
@php $input = 'w-full border-slate-300 rounded-lg text-sm'; $lbl = 'block text-xs font-medium text-slate-500 mb-1'; @endphp

<form method="POST" action="{{ $client->exists ? route('clients.update', $client) : route('clients.store') }}"
      class="bg-white rounded-xl border border-slate-200 p-6 max-w-3xl">
    @csrf
    @if ($client->exists) @method('PUT') @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="{{ $lbl }}">Nome *</label>
            <input type="text" name="name" value="{{ old('name', $client->name) }}" required class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $lbl }}">CPF / CNPJ</label>
            <input type="text" name="document" value="{{ old('document', $client->document) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $lbl }}">E-mail</label>
            <input type="email" name="email" value="{{ old('email', $client->email) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $lbl }}">Telefone</label>
            <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $lbl }}">WhatsApp</label>
            <input type="text" name="whatsapp" value="{{ old('whatsapp', $client->whatsapp) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $lbl }}">Cidade</label>
            <input type="text" name="city" value="{{ old('city', $client->city) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $lbl }}">UF</label>
            <input type="text" name="state" maxlength="2" value="{{ old('state', $client->state) }}" class="{{ $input }}">
        </div>
        <div class="md:col-span-2">
            <label class="{{ $lbl }}">Endereço</label>
            <input type="text" name="address" value="{{ old('address', $client->address) }}" class="{{ $input }}">
        </div>
        <div class="md:col-span-2">
            <label class="{{ $lbl }}">Observações</label>
            <textarea name="notes" rows="3" class="{{ $input }}">{{ old('notes', $client->notes) }}</textarea>
        </div>
    </div>

    <div class="mt-6 flex gap-2">
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-5 py-2 rounded-lg">Salvar</button>
        <a href="{{ route('clients.index') }}" class="text-sm text-slate-500 px-4 py-2">Cancelar</a>
    </div>
</form>
@endsection
