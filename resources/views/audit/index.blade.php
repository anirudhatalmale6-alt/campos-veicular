@extends('layouts.app')
@section('title', 'Log de Auditoria')

@section('content')
    <p class="text-sm text-slate-500 mb-4">Registro de todas as alterações no sistema (quem, o quê, quando).</p>

    <div class="bg-white rounded-xl border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-slate-500 border-b border-slate-100 bg-slate-50">
                <tr>
                    <th class="px-4 py-3 font-medium">Data/Hora</th>
                    <th class="px-4 py-3 font-medium">Usuário</th>
                    <th class="px-4 py-3 font-medium">Evento</th>
                    <th class="px-4 py-3 font-medium">Registro</th>
                    <th class="px-4 py-3 font-medium">Alterações</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $eventLabels = ['created' => 'Criação', 'updated' => 'Alteração', 'deleted' => 'Exclusão'];
                    $eventColors = ['created' => 'bg-emerald-100 text-emerald-700', 'updated' => 'bg-blue-100 text-blue-700', 'deleted' => 'bg-red-100 text-red-700'];
                @endphp
                @forelse ($logs as $log)
                    <tr class="border-b border-slate-50 align-top">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="px-4 py-3">{{ $log->user?->name ?? 'Sistema' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $eventColors[$log->event] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $eventLabels[$log->event] ?? $log->event }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500 max-w-md">
                            @if ($log->new_values)
                                {{ implode(', ', array_keys($log->new_values)) }}
                            @elseif ($log->event === 'deleted')
                                registro removido
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Nenhum registro de auditoria.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
@endsection
